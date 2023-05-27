<?php

declare(strict_types=1);

namespace core\entities\projectile;

use core\utils\MessageUtil;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Snowball as PMSnowball;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class Snowball extends PMSnowball {

    public function initEntity(CompoundTag $nbt) : void {
        parent::initEntity($nbt);
        $this->setScale(0.8);
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