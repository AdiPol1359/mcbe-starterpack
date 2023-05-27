<?php

declare(strict_types=1);

namespace core\entities\projectile;

use core\utils\MessageUtil;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Arrow as PMArrow;
use pocketmine\event\entity\EntityCombustByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\RayTraceResult;
use pocketmine\player\Player;

class Arrow extends PMArrow {

    public function getResultDamage() : int{
        $base = parent::getResultDamage();
        if($this->isCritical()){
            return ($base + mt_rand(0, (int)($base / 2)));
        }else{
            return $base;
        }
    }

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult) : void{
        $damage = $this->getResultDamage();

        if($damage >= 0){
            if(($damager = $this->getOwningEntity()) === null){
                $ev = new EntityDamageByEntityEvent($this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
            }else{
                $ev = new EntityDamageByChildEntityEvent($damager, $this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
            }

            $entityHit->attack($ev);

            if(!$ev->isCancelled()) {
                if($this->isOnFire()){
                    $ev = new EntityCombustByEntityEvent($this, $entityHit, 5);
                    $ev->call();
                    if(!$ev->isCancelled()){
                        $entityHit->setOnFire($ev->getDuration());
                    }
                }

                if($entityHit instanceof Player) {
                    if(!$entityHit->isCreative()) {
                        if($damager instanceof Player) {
                            if($damager->getName() !== $entityHit->getName()) {
                                $health = (float) number_format($entityHit->getHealth(), 2, ".", "");

                                if($health < 0)
                                    $health = 0;

                                $damager->sendMessage(MessageUtil::format("§cZdrowie zaatakowanego gracza wynosi §4" . $health));
                            }
                        }
                    }
                }
            }
        }

        $this->flagForDespawn();
    }
}