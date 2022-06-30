<?php

namespace core\listener\events;

use core\fakeinventory\FakeInventory;
use core\fakeinventory\FakeInventoryAPI;
use core\fakeinventory\inventory\InvSeeInventory;
use core\listener\BaseListener;
use core\Main;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\HopperInventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\PlayerUIInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\scheduler\ClosureTask;

class InventoryListener extends BaseListener{

    /**
     * @param InventoryTransactionEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function onTransaction(InventoryTransactionEvent $e) : void {
        $transaction = $e->getTransaction();
        $player = $transaction->getSource();
        $inventories = $transaction->getInventories();
        $actions = $transaction->getActions();

        if(!FakeInventoryAPI::isOpening($player))
            return;

        $fakeInventory = FakeInventoryAPI::getInventory($player->getName());

        if($fakeInventory === null)
            return;

        foreach($inventories as $inventory) {
            if($inventory instanceof FakeInventory) {
                foreach($actions as $action) {

                    if(!$action instanceof SlotChangeAction)
                        continue;

                    if($action->getInventory() instanceof PlayerInventory || $action->getInventory() instanceof PlayerUIInventory)
                        continue;

                    $e->setCancelled($fakeInventory->onTransaction($player, $action->getSourceItem(), $action->getTargetItem(), $action->getSlot()));
                }
            }
        }
    }

    /**
     * @param InventoryTransactionEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function invSeeTransaction(InventoryTransactionEvent $e) : void{
        $transaction = $e->getTransaction();
        $player = $transaction->getSource();

        foreach(Main::$invSeePlayers as $nick => $inv){
            if(!$inv instanceof InvSeeInventory)
                continue;

            if($player->getName() === $nick)
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($inv) : void{
                    $inv->setItems();
                }), 1);
        }
    }

    /**
     * @param InventoryTransactionEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function hopperTransaction(InventoryTransactionEvent $e) : void {
        $transaction = $e->getTransaction();
        $player = $transaction->getSource();
        $inventories = $transaction->getInventories();

        if(!FakeInventoryAPI::isOpening($player))
            return;

        $fakeInventory = FakeInventoryAPI::getInventory($player->getName());

        if($fakeInventory === null)
            return;

        foreach($inventories as $inventory) {
            if($inventory instanceof HopperInventory)
                $e->setCancelled(true);
        }
    }
}