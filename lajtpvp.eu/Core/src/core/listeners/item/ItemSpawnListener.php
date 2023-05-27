<?php

declare(strict_types=1);

namespace core\listeners\item;

use core\Main;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\event\Listener;

class ItemSpawnListener implements Listener {

    public function abyssItem(ItemSpawnEvent $e) : void {
        Main::getInstance()->getAbyssManager()->addItemEntity($e->getEntity());

        $entity = $e->getEntity();
        if($entity instanceof ItemEntity) {
            $entities = $entity->getWorld()->getNearbyEntities($entity->getBoundingBox()->expandedCopy(5, 5, 5));
            $originalItem = $entity->getItem();
            foreach($entities as $ent) {
                if($ent instanceof ItemEntity && $entity->getId() !== $ent->getId()) {
                    $item = $ent->getItem();
                    if($item->equals($originalItem)) {
                        $ent->flagForDespawn();
                        $entity->getItem()->setCount($originalItem->getCount() + $item->getCount());
                        $item->setCount(0);
                    }
                }
            }
        }
    }
}