<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class SecondInventory extends FakeInventory {

    public function __construct() {
        parent::__construct("Second inventory");
    }

    public function setItems() : void {
        $this->setItem(0, VanillaItems::GOLD_INGOT());
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        if($sourceItem->getId() === ItemIds::GOLD_INGOT) {
            $this->changeInventory($player, new SmallInventory());
        }

        return true;
    }
}