<?php

declare(strict_types=1);

namespace core\listeners\inventory;

use core\Main;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\player\Player;

class PickupItemListener implements Listener {

    public function refreshInventory(PlayerBlockPickEvent $e) : void {

        //TOOD: inventory gildii
//        $player = $e->getPlayer();
//        if(!FakeInventoryManager::isOpening($player))
//            return;
//
//        $inventory = FakeInventoryManager::getInventory($player);
//
//        if(!$inventory instanceof ItemsInventory)
//            return;
//
//        $inventory->setItems();
    }

    public function vanishPickupItem(EntityItemPickupEvent $e) : void {
        $entity = $e->getEntity();

        if(!$entity instanceof Player) {
            return;
        }

        if(($user = Main::getInstance()->getUserManager()->getUser($entity->getName()))) {
            if($user->isVanished()) {
                $e->cancel();
            }
        }
    }
}