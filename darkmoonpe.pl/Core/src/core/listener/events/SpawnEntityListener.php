<?php

namespace core\listener\events;

use core\entity\entities\custom\CaveSpawn;
use core\entity\entities\mobs\Villager;
use core\listener\BaseListener;
use core\Main;
use core\manager\managers\MobStackerManager;
use core\manager\managers\pet\PetEntity;
use core\util\utils\ConfigUtil;
use pocketmine\entity\Animal;
use pocketmine\entity\Mob;
use pocketmine\entity\object\ArmorStand;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;

class SpawnEntityListener extends BaseListener{
    public function mobLimit(EntitySpawnEvent $e) : void {

        $entity = $e->getEntity();

        $count = 0;

        foreach($entity->getLevel()->getEntities() as $entity) {
            if(!$entity instanceof CaveSpawn && !$entity instanceof Villager && !$entity instanceof ItemEntity && !$entity instanceof ArmorStand && !$entity instanceof Player && !$entity instanceof PetEntity && !$entity instanceof Arrow)
                $count++;
        }

        if($count > ConfigUtil::ENTITIES_LIMIT) {
            $entity->getLevel()->removeEntity($entity);

            Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                function() use ($entity): void {
                    $entity->close();
                }
            ), 3);
        }
    }

    public function blockArmorStand(EntitySpawnEvent $e) : void{

        $entity = $e->getEntity();

        if($entity instanceof ArmorStand) {
            Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                function() use ($entity) : void {
                    $entity->close();
                }
            ), 3);
        }
    }

    public function mobDefaultLevel(EntitySpawnEvent $e) : void{
        if($e->getEntity()->getLevel() === ConfigUtil::DEFAULT_WORLD){
            foreach($this->getServer()->getLevelByName(ConfigUtil::DEFAULT_WORLD)->getEntities() as $entity){
                if($entity instanceof Mob && !$entity instanceof ItemEntity && !$entity instanceof Player)
                    $entity->close();
            }
        }
    }

    public function StackSpawnEvent(EntitySpawnEvent $e): void{
        $entity = $e->getEntity();
        if($entity instanceof Player || !$entity instanceof Animal || $entity instanceof ArmorStand || $entity instanceof ItemEntity || $entity instanceof ExperienceOrb)
            return;

        $mobstacker = new MobStackerManager($entity);
        $mobstacker->Stack();
    }
}