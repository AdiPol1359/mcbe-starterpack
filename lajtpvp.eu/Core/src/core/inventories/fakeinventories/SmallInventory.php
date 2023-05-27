<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\inventories\FakeInventorySize;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class SmallInventory extends FakeInventory {

    public function __construct(string $title = "Fake Inventory", int $size = FakeInventorySize::LARGE_CHEST) {
        parent::__construct($title, $size);
    }

    public function setItems() : void {
        $this->setItem(0, VanillaItems::EMERALD());
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        if($sourceItem->getId() === 388) {
            $this->changeInventory($player, new SecondInventory());
        }

        return true;
    }
}