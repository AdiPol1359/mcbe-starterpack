<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryManager;
use core\inventories\FakeInventorySize;
use core\Main;
use core\utils\InventoryUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;

class AbyssInventory extends FakeInventory {

    public function __construct() {
        parent::__construct("§l§eOTCHLAN", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void {

        for($i = 0; $i <= 53; $i++)
            $this->setItem($i, VanillaBlocks::AIR()->asItem(), true, false);

        foreach(Main::getInstance()->getAbyssManager()->getItems() as $slot => $item) {
            if($slot >= 53)
                return;

            $this->setItem($slot, $item, true, false);
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $abyssManager = Main::getInstance()->getAbyssManager();

        if($abyssManager->checkItem($slot, $sourceItem)) {
            $abyssManager->removeItemBySlot($slot);

            foreach(FakeInventoryManager::getInventories() as $playerInv => $inventory) {
                if($inventory instanceof AbyssInventory)
                    $inventory->setItems();
            }

            InventoryUtil::addItem($sourceItem, $player);
            $this->unClickItem($player);
            return true;
        }

        return true;
    }
}