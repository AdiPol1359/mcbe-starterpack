<?php

namespace core\manager\managers\pet;

use core\util\utils\ConfigUtil;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\entity\Creature;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

class PetEntity extends Creature {

    private Pet $pet;
    private Vector3 $lastPosition;

    public function __construct(Level $level, CompoundTag $nbt, Pet $pet) {
        $this->pet = $pet;

        $pet->setEntity($this);

        $this->width = $pet->getWidth();
        $this->height = $pet->getHeight();

        if($this->pet->canFly())
            $this->gravity = 0.02;

        parent::__construct($level, $nbt);
    }

    public function getName() : string {
        return $this->pet->getName();
    }

    public function saveNBT() : void {}

    protected function sendSpawnPacket(Player $player): void {

        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type = AddActorPacket::LEGACY_ID_MAP_BC[$this->pet->getNetworkID()];
        $pk->position = $this->asVector3();
        $pk->motion = $this->getMotion();
        $pk->yaw = $this->yaw;
        $pk->headYaw = $this->yaw;
        $pk->pitch = $this->pitch;
        $pk->attributes = $this->attributeMap->getAll();
        $pk->metadata = $this->propertyManager->getAll();

        $player->dataPacket($pk);
    }

    public function onUpdate(int $currentTick) : bool {

        $owner = $this->pet->getOwner();

        if($owner === null || !$owner->isOnline()) {
            $this->close();
            return parent::onUpdate($currentTick);
        }

        if($owner->getLevel() !== $this->getLevel() || $owner->distance($this->asVector3()) >= 20)
            $this->teleport($owner->asPosition());

        if($this->pet->canFly()) {
            if(($owner->y + 2) > $this->y) {
                $this->moveFlying($this->motion->x, $this->motion->y, $this->motion->z);
                $this->addMotion(0, 0.03, 0);
            }
        }

        switch($this->getDirection()) {
            case 0:
                $blockPos = $this->add(1);
                break;
            case 1:
                $blockPos = $this->add(0, 0, 1);
                break;
            case 2:
                $blockPos = $this->add(-1);
                break;
            case 3:
                $blockPos = $this->add(0, 0, -1);
                break;
            default:
                return false;
        }

        $block = $this->level->getBlock($blockPos);

        if(!$block instanceof Flowable && $block->getId() !== Block::AIR && $this->isOnGround() && !$this->pet->canFly())
            $this->jump();

        $this->followOwner();

        return parent::onUpdate($currentTick);
    }

    private function followOwner() : void {

        $owner = $this->pet->getOwner();

        if(isset($this->lastPosition) && $this->lastPosition->equals($owner->asVector3())){
            if($this->pet->canFly()) {
                $this->resetMotion();
                if(($owner->y + 2) > $this->y) {
                    $this->moveFlying($this->motion->x, $this->motion->y, $this->motion->z);
                    $this->addMotion(0, 0.03, 0);
                }
            }
            return;
        }

        if($this->distance($owner) < 3) {
            $this->lastPosition = $owner->asVector3();
            return;
        }

        $x = floor($owner->x - $this->x);
        $z = floor($owner->z - $this->z);

        $xz = sqrt($x * $x + $z * $z);

        if($xz == 0)
            return;

        if($this->pet->getNetworkID() === 18)
            $this->jump();

        $this->pet->getOwner()->getMovementSpeed() < 1 ? $petSpeed = $this->pet->getSpeed() : $petSpeed = $this->pet->getOwner()->getMovementSpeed();

        if(($distance = $this->pet->getOwner()->distance($this->asVector3()->round())) <= 5) {

            for($dist = 7; $distance >= 1; $dist--)
                $distance--;

            $petSpeed = $petSpeed / $dist;
        }

        $speed = $petSpeed * 0.15;
        $this->motion->x = $speed * ($x / $xz);
        $this->motion->z = $speed * ($z / $xz);

        $this->setHeadOnOwner();

        $this->move($this->motion->x, $this->motion->y, $this->motion->z);
    }

    private function setHeadOnOwner() : void {

        $owner = $this->pet->getOwner();

        $x = $owner->getX() - $this->getX();
        $y = $owner->getY() - $this->getY();
        $z = $owner->getZ() - $this->getZ();

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

    public function attack(EntityDamageEvent $source) : void {
        $source->setCancelled();
    }

    public function spawnTo(Player $player) : void {

        $owner = $this->pet->getOwner();

        if($owner) {
            if($owner->getLevel()->getName() === ConfigUtil::PVP_WORLD || $owner->getLevel()->getName() === ConfigUtil::LOBBY_WORLD || $owner->getLevel()->getName() === ConfigUtil::BOSS_WORLD) {
                $this->despawnFromAll();
                return;
            }
        }

        parent::spawnTo($player);
    }

    public function teleport(Vector3 $pos, ?float $yaw = null, ?float $pitch = null) : bool {

        $owner = $this->pet->getOwner();

        if($owner) {
            if($owner->getLevel()->getName() === ConfigUtil::PVP_WORLD || $owner->getLevel()->getName() === ConfigUtil::LOBBY_WORLD || $owner->getLevel()->getName() === ConfigUtil::BOSS_WORLD)
                $this->despawnFromAll();
            else
                $this->spawnToAll();
        }

        return parent::teleport($pos, $yaw, $pitch);
    }
}