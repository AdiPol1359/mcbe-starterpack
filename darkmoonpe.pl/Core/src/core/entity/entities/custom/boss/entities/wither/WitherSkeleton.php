<?php

namespace core\entity\entities\custom\boss\entities\wither;

use pocketmine\entity\Monster;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\Player;

class WitherSkeleton extends Monster {

    public const NETWORK_ID = self::WITHER_SKELETON;

    protected ?Player $closestPlayer = null;

    public function __construct(Level $level, CompoundTag $nbt) {

        $this->height = 2.412;
        $this->width = 0.864;

        parent::__construct($level, $nbt);
    }

    public function onUpdate(int $currentTick) : bool {
        $this->updateClosestPlayer();
        $this->follow();

        return parent::onUpdate($currentTick);
    }

    private function updateClosestPlayer() : void {
        $closestPlayer = null;

        foreach($this->level->getPlayers() as $player) {

            if($player->isCreative())
                continue;

            if(!$closestPlayer)
                $closestPlayer = $player;

            if($closestPlayer->distance($this) > $player->distance($this))
                $closestPlayer = $player;
        }

        $this->closestPlayer = $closestPlayer;
    }

    private function follow() : void {

        if(!$this->closestPlayer)
            return;

        $this->setHeadOnOwner();

        if($this->closestPlayer->distance($this) <= 2.9)
            $this->closestPlayer->attack(new EntityDamageByEntityEvent($this, $this->closestPlayer, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 15));

        $x = floor($this->closestPlayer->x - $this->x);
        $z = floor($this->closestPlayer->z - $this->z);

        $xz = sqrt($x * $x + $z * $z);

        if($xz == 0)
            return;

        $speed = 0.15;
        $this->motion->x = $speed * ($x / $xz);
        $this->motion->z = $speed * ($z / $xz);

        $this->move($this->motion->x, $this->motion->y, $this->motion->z);
    }

    private function setHeadOnOwner() : void {

        if(!$this->closestPlayer)
            return;

        $x = $this->closestPlayer->getX() - $this->getX();
        $y = $this->closestPlayer->getY() - $this->getY();
        $z = $this->closestPlayer->getZ() - $this->getZ();

        $len = sqrt($x * $x + $y + $z * $z);

        if($len == 0)
            return;

        $y = $y / $len;

        $pitch = -(asin($y) * 180 / M_PI);

        $yaw = -atan2($x, $z) * (180 / M_PI);

        if(!($pitch < 89) && !($pitch > -89))
            return;

        $this->yaw = $yaw;
        $this->pitch = $pitch;
    }

    public function spawnTo(Player $player) : void {
        parent::spawnTo($player);

        $pk = new MobEquipmentPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->item = ItemStackWrapper::legacy(Item::get(Item::STONE_SWORD));
        $pk->inventorySlot = 0;
        $pk->hotbarSlot = 0;

        $player->dataPacket($pk);
    }

    public function getName() : string {
        return "Wither Skeleton";
    }
}