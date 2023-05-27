<?php

namespace core\entities\custom;

use core\inventories\fakeinventories\guild\panel\MainPanelInventory;
use core\guilds\Guild;
use core\Main;
use core\utils\MessageUtil;
use core\utils\Settings;
use core\utils\SkinUtil;
use core\utils\SoundUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class GuildHeart extends Human {

    public const HEAD_GEOMETRY = '{
	"geometry.player_head": {
		"texturewidth": 64,
		"textureheight": 64,
		"bones": [
			{
				"name": "head",
				"pivot": [0, 0, 0],
				"cubes": [
					{"origin": [-4, 0.5, -4], "size": [8, 8, 8], "uv": [0, 0]},
					{"origin": [-4, 0.5, -4], "size": [8, 8, 8], "uv": [32, 0], "inflate": 0.5}
				]
			}
		]
	}
}';

    public $width = 0.5;
    public $height = 0.6;
    public $gravity = 0;

    private int $attackDelay = 0;

    private string $tag;

    private float $spawnY;
    private bool $up = false;

    private ?Guild $guild;

    public function __construct(Location $location, Skin $skin, CompoundTag $nbt) {
        $close = false;
        $guild = Main::getInstance()->getGuildManager()->getGuild($nbt->getString("guild"));

        if($guild === null) {
            $close = true;
            return;
        }

        $guild->setGuildHeart($this);
        $this->guild = $guild;
        $this->tag = $guild->getTag();

        $location->getWorld()->getChunk($location->getX(), $location->getZ());
        $location->getWorld()->loadChunk($location->getX() >> 4,$location->getZ() >> 4);

        parent::__construct($location, $skin, $nbt);

        if($close) {
            $this->close();
        }
    }

    public function hasMovementUpdate() : bool {
        return true;
    }

    public function canBeMovedByCurrents() : bool {
        return false;
    }

    public function canBePushed() : bool {
        return false;
    }

    public function canSaveWithChunk(): bool {
        return true;
    }

    protected function initEntity(CompoundTag $nbt) : void {
        $skin = SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder()."/default/guildHeart.png");

        $this->setMaxHealth(1);
        $this->setSkin(new Skin($this->getSkin()->getSkinId(), $skin));
        $this->updateTag();
        $this->spawnY = $this->guild->getHeartSpawn()->y + 0.25;

        parent::initEntity($nbt);
    }

    public function updateTag() : void {
        $tag =
            "§l§8[§7----===§8[§e".$this->guild->getTag()."§8]§7===----§8]§r\n".
            "§7Ochrona§8: §e".(date("d.m.Y H:i", $this->guild->getConquerTime()))."§r\n".
            "§7Wygasa§8: §e".(date("d.m.Y H:i", $this->guild->getExpireTime()))."§r\n".
            "§7Zdrowie§8: §8(§e".$this->guild->getHealth()."§7/§e".Settings::$MAX_GUILD_HEALTH."§8)§r\n".
            "§l§e".str_repeat("❤", $this->guild->getHearts())."§8".str_repeat("❤", (5 - $this->guild->getHearts())).
            "§r";

        $this->setNameTagAlwaysVisible(true);
        $this->setNameTag($tag);
    }

    public function setSkin(Skin $skin) : void {
        parent::setSkin(new Skin($skin->getSkinId(), $skin->getSkinData(), '', 'geometry.player_head', self::HEAD_GEOMETRY));
    }

    public function entityBaseTick(int $tickDiff = 1) : bool {
        if($this->attackDelay < Settings::$HEART_REGEN_START)
            $this->attackDelay++;

        if($this->attackDelay >= Settings::$HEART_REGEN_START && $this->guild->getHealth() < Settings::$MAX_GUILD_HEALTH) {
            $this->guild->addHeath();
            $this->attackDelay = Settings::$HEART_ATTACK_DELAY;
        }

        $actualPosition = $this->getPosition();
        $y = $this->getPosition()->y;
        $min = ($this->spawnY + 1) - 0.05;
        $max = ($this->spawnY + 1) + 0.05;

        if($this->up) {
            $this->setPosition(new Vector3($actualPosition->x, $y + 0.005, $actualPosition->z));
            if($y > $max)
                $this->up = false;
        } else {
            $this->setPosition(new Vector3($actualPosition->x, $y - 0.005, $actualPosition->z));
            $this->getPosition()->y -= 0.005;
            if($y < $min)
                $this->up = true;
        }

        if($this->getLocation()->yaw > 360) {
            $this->setRotation(0, 0);
            $this->getLocation()->yaw = 0;
        } else {
            $this->setRotation($this->getLocation()->yaw + 5, 0);
        }

        return parent::entityBaseTick($tickDiff);
    }

    public function attack(EntityDamageEvent $source) : void {
        if(!$source instanceof EntityDamageByEntityEvent)
            return;

        $position = $this->getPosition();

        if($this->guild === null) {
            $this->close();
            return;
        }

        if($this->guild->getGuildHeart()->getId() !== $this->id) {
            $this->close();
            return;
        }

        $source->getDamager() instanceof Player ? $damager = $source->getDamager() : $damager = $source->getDamager()->getOwningEntity();

        if(!$damager instanceof Player)
            return;

        if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($damager->getName())) === null) {
            $damager->sendMessage(MessageUtil::format("Nie mozesz podbic gildii poniewaz nie znajdujesz sie w zadnej!"));
            return;
        }

        if($guild->getTag() === $this->guild->getTag()) {
            $damager->sendMessage(MessageUtil::format("Nie mozesz atakowac wlasnej gildii!"));
            return;
        }

        if($this->guild->isAlliance($guild->getTag())) {
            $damager->sendMessage(MessageUtil::format("Nie mozesz podbic sojuszniczej gildii!"));
            return;
        }

        if(($war = Main::getInstance()->getWarManager()->getWar($this->guild->getTag())) === null) {
            $damager->sendMessage(MessageUtil::format("Twoja gildia nie wypowiedziala wojny tej gildii!"));
            return;
        }

        if($war->getAttacker() !== $guild->getTag() && $war->getAttacked() !== $guild->getTag()) {
            $damager->sendMessage(MessageUtil::format("Nie mozesz zaatakowac tej gildii poniewaz nie jestes uczestnikiem wojny!"));
            return;
        }

        if($war->getStartTime() > time()) {
            $damager->sendMessage(MessageUtil::format("Wojna sie jeszcze nie rozpoczela!"));
            return;
        }

        if($war->getEndTime() < time()) {
            $damager->sendMessage(MessageUtil::format("Wojna sie juz skonczyla!"));
            return;
        }

        if($this->attackDelay < Settings::$HEART_ATTACK_DELAY)
            return;

        if($this->guild->getConquerTime() > time()) {
            $damager->sendMessage(MessageUtil::format("Nie mozesz jeszcze podbic tej gildii!"));
            return;
        }

        if(!$this->guild->getGuildGolem()) {
            $nbt = CompoundTag::create()
                ->setString("guild", $this->guild->getTag());

            $golem = new GuildGolem($this->location, $nbt);
            $golem->spawnToAll();
        }

        if($this->guild->getGuildGolem()->isAlive()) {

            $user = Main::getInstance()->getUserManager()->getUser($damager->getName());

            if(!$user->hasLastData(Settings::$GOLEM_DELAY_MESSAGE)) {
                $damager->sendMessage(MessageUtil::format("Nie mozesz zaatakowac gildii poniewaz golem nie zostal jeszcze zabity!"));
                $user->setLastData(Settings::$GOLEM_DELAY_MESSAGE, (time() + Settings::$GOLEM_DELAY_MESSAGE_TIME), Settings::$TIME_TYPE);
            }

            return;
        }

        SoundUtil::addSound([$damager], $damager->getPosition(), "game.player.attack.strong", 5, 2);

        $deltaX = $damager->x - $position->x;
        $deltaZ = $damager->z - $position->z;

        $damager->knockBack($deltaX, $deltaZ, 0.3);

        $this->guild->attack($damager, ceil($source->getFinalDamage()));

        $this->attackDelay = 0;

        $this->updateTag();
        $this->doHitAnimation();
        $source->cancel();
    }

    public function knockBack(float $x, float $z, float $force = 0.4, ?float $verticalLimit = 0.4) : void {}

    public function setMotion(Vector3 $motion) : bool {
        return false;
    }

    public function getGuild() : ?Guild {
        return $this->guild;
    }

    #[Pure] public function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(0.5, 0.5);
    }

    public function setOnFire(int $seconds) : void {}

//    public function onFirstInteract(Player $player) : void {
//        if($this->guild) {
//            if($this->guild->existsPlayer($player->getName())) {
//                if($this->guild->getPlayer($player->getName())->getRank() === GuildPlayer::LEADER) {
//                    (new MainPanelInventory($player, $this->guild))->openFor([$player]);
//                }
//            }
//        }
//    }
}