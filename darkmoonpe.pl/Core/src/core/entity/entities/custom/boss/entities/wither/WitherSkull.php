<?php

namespace core\entity\entities\custom\boss\entities\wither;

use core\manager\managers\ParticlesManager;
use core\manager\managers\SoundManager;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\level\Level;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class WitherSkull extends Throwable {

    protected ?Player $closestPlayer = null;

    public const NETWORK_ID = self::WITHER_SKULL;

    public function __construct(Level $level, CompoundTag $nbt, ?Entity $shootingEntity = null) {
        parent::__construct($level, $nbt, $shootingEntity);

        $this->setScale(2);
    }

    public function onUpdate(int $currentTick) : bool {
        $this->updateClosestPlayer();
        $this->gravity = 0.02;

        if($this->closestPlayer) {
            if(($this->closestPlayer->y + 2) > $this->y) {
                $this->moveFlying($this->motion->x, $this->motion->y, $this->motion->z);
                $this->addMotion(0, 0.03, 0);
            }
        }

        $this->follow();

        return parent::onUpdate($currentTick);
    }

    protected function onHit(ProjectileHitEvent $event) : void{

        foreach($this->getLevel()->getPlayers() as $player) {

            if($player->distance($this) <= 7)
                $player->attack(new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 15));

            ParticlesManager::spawnParticle($player, new HugeExplodeParticle($this->asPosition()));
            SoundManager::spawnSpecifySound($this->asPosition(), $this->getLevel()->getPlayers(), "random.explode");
            
            return;
        }
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

        if($this->distance($this->closestPlayer) <= 3) {
            foreach($this->getLevel()->getPlayers() as $player) {

                if($player->distance($this) <= 7)
                    $player->attack(new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 15));

                ParticlesManager::spawnParticle($player, new HugeExplodeParticle($this->asPosition()));
                SoundManager::spawnSpecifySound($this->asPosition(), $this->getLevel()->getPlayers(), "random.explode");

                $this->close();
                return;
            }
        }

        $this->setHeadOnOwner();

        $x = floor($this->closestPlayer->x - $this->x);
        $z = floor($this->closestPlayer->z - $this->z);

        $xz = sqrt($x * $x + $z * $z);

        if($xz == 0)
            return;

        $speed = 0.5;
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
}