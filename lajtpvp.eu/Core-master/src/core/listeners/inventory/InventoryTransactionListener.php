<?php

declare(strict_types=1);

namespace core\listeners\inventory;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryManager;
use pocketmine\block\Transparent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\PlayerOffHandInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\EnderPearl;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ContainerIds;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;

class InventoryTransactionListener implements Listener {

    /**
     * @param InventoryTransactionEvent $e
     * @priority NORMAL
     * @ignoreCancelled true
     */

    public function onTransaction(InventoryTransactionEvent $e) : void {
        $transaction = $e->getTransaction();
        $player = $transaction->getSource();
        $inventories = $transaction->getInventories();
        $actions = $transaction->getActions();

        $fakeInventory = FakeInventoryManager::getInventory($player->getName());

        foreach($inventories as $inventory) {
            if($inventory instanceof FakeInventory) {
                if($fakeInventory === null) {
                    $e->cancel();
                    return;
                }

                foreach($actions as $action) {
                    if(!$action instanceof SlotChangeAction || $action->getInventory() !== $inventory)
                        continue;

                    $fakeInventory->onTransaction($player, $action->getSourceItem(), $action->getTargetItem(), $action->getSlot()) ? $e->cancel() : $e->uncancel();
                }
            }
        }
    }

    public function blockPlayerSlot(InventoryTransactionEvent $e) : void {
        $transaction = $e->getTransaction();
        $inventories = $transaction->getInventories();
        $actions = $transaction->getActions();

        foreach($inventories as $inventory) {
            if($inventory instanceof PlayerOffHandInventory) {
                foreach($actions as $action) {
                    if(!$action instanceof SlotChangeAction) {
                        continue;
                    }

                    if($action->getInventory() instanceof PlayerOffHandInventory) {
                        $e->cancel();
                    }
                }
            }
        }
    }

    /**
     * @param InventoryTransactionEvent $e
     * @priority LOW
     * @ignoreCancelled true
     */

    public function enderPearlInBlocks(InventoryTransactionEvent $e) : void {
        $transaction = $e->getTransaction();
        $player = $transaction->getSource();
        $actions = $transaction->getActions();

        foreach($actions as $action) {
            if(!$action instanceof SlotChangeAction)
                continue;

            if($action->getInventory() instanceof PlayerInventory)
                continue;

            $block = $player->getPosition()->getWorld()->getBlock($player->getPosition()->floor());
            $blockUp = $player->getPosition()->getWorld()->getBlock($player->getPosition()->floor());

            if(!$action->getSourceItem() instanceof EnderPearl || $block instanceof Transparent || $blockUp instanceof Transparent)
                continue;

            $e->cancel();

            $packet = new InventorySlotPacket();
            $packet->windowId = ContainerIds::UI;
            $packet->inventorySlot = 0;
            $packet->item = ItemStackWrapper::legacy(ItemStack::null());
            $player->getNetworkSession()->sendDataPacket($packet);
        }
    }
}