<?php

namespace core\entity\entities\custom;

use core\caveblock\Cave;
use core\caveblock\CaveManager;
use core\form\forms\caveblock\ManageCave;
use core\Main;
use core\util\utils\ConfigUtil;
use core\util\utils\SkinUtil;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class CaveSpawn extends Human {
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
    private string $tag;
    private float $spawnY;
    private bool $up = false;
    private ?Cave $cave;

    public function __construct(Level $level, CompoundTag $nbt) {
        $this->tag = str_replace(ConfigUtil::LEVEL, "", $level->getName());
        $this->cave = CaveManager::getCaveByTag($this->tag);

        parent::__construct($level, $nbt);
    }

    public function hasMovementUpdate() : bool {
        return true;
    }

    public function onInteract(Player $player, Item $item, Vector3 $clickPos) : bool {
        if($this->cave->isMember($player->getName())) {
            $player->sendForm(new ManageCave($player, CaveManager::getCaveByTag($this->tag)));
            return parent::onInteract($player, $item, $clickPos);
        }

        return parent::onInteract($player, $item, $clickPos);
    }

    public function getCaveTag() : ?string {
        return $this->tag;
    }

    public function canBeMovedByCurrents() : bool {
        return false;
    }

    public function canBePushed() : bool {
        return false;
    }

    protected function initEntity() : void {

        is_file(Main::getInstance()->getDataFolder()."/playersSkins/".$this->cave->getOwner().".png") ? $skin = SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder()."/playersSkins/".$this->cave->getOwner().".png") : $skin = SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder()."/default/defaultSkin.png");

        $this->setMaxHealth(1);
        $this->setSkin(new Skin($this->getSkin()->getSkinId(), $skin));
        $tag = "§l§9SPAWN JASKINI!\n" . "\n\n§r§7" . "Tag: §l§9" . $this->tag . "§r§7\n" . "Wlasicicel: §9§l" . $this->cave->getOwner();
        $this->setNameTag($tag);
        $this->spawnY = $this->cave->getSpawn()->y;
        $this->entityBaseTick();

        parent::initEntity();
    }

    public function setSkin(Skin $skin) : void {
        parent::setSkin(new Skin($skin->getSkinId(), $skin->getSkinData(), '', 'geometry.player_head', self::HEAD_GEOMETRY));
    }

    public function entityBaseTick(int $tickDiff = 1) : bool {

        $y = $this->y;
        $min = ($this->spawnY + 1) - 0.05;
        $max = ($this->spawnY + 1) + 0.05;

        if($this->up) {
            $this->y += 0.005;
            if($y > $max)
                $this->up = false;
        } else {
            $this->y -= 0.005;
            if($y < $min)
                $this->up = true;
        }

        if($this->yaw > 360)
            $this->yaw = 0;
        else
            $this->yaw += 5;

        return parent::entityBaseTick($tickDiff);
    }
}