<?php

namespace core\entities\custom;

use core\guilds\Guild;
use core\Main;
use core\utils\Settings;
use JetBrains\PhpStorm\Pure;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\world\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class GuildGolem extends Living {

    protected ?Player $closestPlayer = null;
    protected ?Guild $guild = null;

    private int $lastAttackTick = 0;
    private int $lastAttackerTick = 0;

    public function __construct(Location $location, CompoundTag $nbt) {
        parent::__construct($location, $nbt);

        if(!$nbt->getTag("guild")) {
            $this->close();
            return;
        }

        $guildTag = $nbt->getString("guild");

        if(($guild = Main::getInstance()->getGuildManager()->getGuild($guildTag)) === null) {
            $this->close();
            return;
        }

        $this->setScale(0.9);

        $this->guild = $guild;

        $this->setMaxHealth($this->guild->getGolemHealth());
        $this->setHealth($this->getMaxHealth());

        $this->setNameTag("§l§eOCHRONIARZ §r§8(§e".$this->getHealth()."§7/§e".$this->getMaxHealth()."§8)");

        $guild->setGuildGolem($this);
    }

    public function onUpdate(int $currentTick) : bool {

        if($this->guild === null) {
            $this->close();
            return false;
        }

        if($this->lastAttackerTick >= 20*Settings::$GOLEM_CLOSE_TIME && $this->isAlive()) {
            $this->guild->setGuildGolem(null);
            $this->close();
            return false;
        }

        $this->updateClosestPlayer();
        $this->follow();
        $this->lastAttackTick++;
        $this->lastAttackerTick++;

        return parent::onUpdate($currentTick);
    }

    private function updateClosestPlayer() : void {

        if(!$this->guild) {
            $this->close();
            return;
        }

        $closestPlayer = null;

        foreach($this->getWorld()->getPlayers() as $player) {

            if($player->isCreative())
                continue;

            if($this->guild->existsPlayer($player->getName()) || $this->guild->isAlliance($player->getName()))
                continue;

            if(!$this->guild->isInHeart($player->getPosition()))
                continue;

            if(!$closestPlayer)
                $closestPlayer = $player;

            if($closestPlayer->getPosition()->distance($this->guild->getGuildHeart()->getPosition()) > $player->getPosition()->distance($this->guild->getGuildHeart()->getPosition()))
                $closestPlayer = $player;
        }

        $this->closestPlayer = $closestPlayer;
    }

    private function follow() : void {

        if(!$this->closestPlayer)
            return;

        $this->setHeadOnOwner();

        if($this->closestPlayer->getPosition()->distance($this->getPosition()) <= 2.9) {
            if($this->lastAttackTick >= 20) {
                if(!$this->isAlive())
                    return;

                $this->closestPlayer->attack(new EntityDamageByEntityEvent($this, $this->closestPlayer, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 10));
                $this->lastAttackTick = 0;
            }
        }

        $x = floor($this->closestPlayer->getPosition()->x - $this->getPosition()->x);
        $z = floor($this->closestPlayer->getPosition()->z - $this->getPosition()->z);

        $xz = sqrt($x * $x + $z * $z);

        if($xz == 0)
            return;

        $speed = 0.05;
        $this->motion->x = $speed * ($x / $xz);
        $this->motion->z = $speed * ($z / $xz);

        $this->move($this->motion->x, $this->motion->y, $this->motion->z);
    }

    public function move(float $dx, float $dy, float $dz) : void {

        $actualPos = $this->getPosition();

        if($actualPos->x > 0)
            $actualPos->add(ceil($dx), 0, 0);

        if($actualPos->y > 0)
            $actualPos->add(0, ceil($dy),0);

        if($actualPos->z > 0)
            $actualPos->add(0, 0, ceil($dz));

        if(!$this->guild->isInHeart(Position::fromObject($actualPos, $actualPos->world))) {

            $x = floor($this->guild->getHeartSpawn()->x - $this->getPosition()->x);
            $z = floor($this->guild->getHeartSpawn()->z - $this->getPosition()->z);

            $xz = sqrt($x * $x + $z * $z);

            if($xz == 0)
                return;

            $speed = 0.05;
            $this->motion->x = $speed * ($x / $xz);
            $this->motion->z = $speed * ($z / $xz);

            $dx = $this->motion->x;
            $dy = $this->motion->y;
            $dz = $this->motion->z;
        }

        parent::move($dx, $dy, $dz);
    }

    private function setHeadOnOwner() : void {

        if(!$this->closestPlayer)
            return;

        $x = $this->closestPlayer->getPosition()->getX() - $this->getPosition()->getX();
        $y = $this->closestPlayer->getPosition()->getY() - $this->getPosition()->getY();
        $z = $this->closestPlayer->getPosition()->getZ() - $this->getPosition()->getZ();

        $len = sqrt($x * $x + $y + $z * $z);

        if($len == 0)
            return;

        $y = $y / $len;

        $pitch = -(asin($y) * 180 / M_PI);

        $yaw = -atan2($x, $z) * (180 / M_PI);

        if(!($pitch < 89) && !($pitch > -89))
            return;

        $this->location->yaw = $yaw;
        $this->location->pitch = $pitch;
    }

    public function spawnTo(Player $player) : void {
        parent::spawnTo($player);

        //TODO: po co ten miecz
//        $pk = new MobEquipmentPacket();
//        $pk->actorRuntimeId = $this->getId();
//        $pk->item = ItemStackWrapper::legacy(Item::get(Item::STONE_SWORD));
//        $pk->inventorySlot = 0;
//        $pk->hotbarSlot = 0;
//
//        $player->dataPacket($pk);
    }

    public function getName() : string {
        return "Guild Golem";
    }

    public function attack(EntityDamageEvent $source) : void {

        if($source instanceof EntityDamageByEntityEvent && $this->guild) {
            $damager = $source->getDamager();

            if($damager instanceof Player) {
                if($this->guild->existsPlayer($damager->getName()) || $this->guild->isAlliancePlayer($damager->getName()))
                    return;
            }
        }

        $this->doHitAnimation();
        $this->lastAttackerTick = 0;

        $this->updateTag();
    }

    public function updateTag() : void {
        $this->setNameTag("§l§eOCHRONIARZ §r§8(§e".$this->getHealth()."§7/§e".$this->getMaxHealth()."§8)");
    }

    #[Pure] protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(2.9, 1.4);
    }

    public static function getNetworkTypeId() : string {
        return EntityLegacyIds::IRON_GOLEM;
    }
}