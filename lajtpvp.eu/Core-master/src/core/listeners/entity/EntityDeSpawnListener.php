<?php

declare(strict_types=1);

namespace core\listeners\entity;

use core\Main;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\Listener;

class EntityDeSpawnListener implements Listener {

    public function test(EntityDespawnEvent $e) : void {
        $entity = $e->getEntity();

        if($entity instanceof ItemEntity) {
            $abyssManager = Main::getInstance()->getAbyssManager();

            if($abyssManager->getItemEntity($entity->getId())) {
                $abyssManager->removeItemEntity($entity->getId());
            }
        }
    }
}