<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\drop;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryPatterns;
use core\inventories\FakeInventorySize;
use core\items\custom\CustomItem;
use core\utils\Settings;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class DropCobblexInventory extends FakeInventory {

    public function __construct() {
        parent::__construct("§l§eDROP Z COBBLEXA", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void{
        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_UP_AND_DOWN);

        $this->setItem(49, VanillaItems::NETHER_STAR()->setCustomName("§r§l§cPOWROT"), true, true);

        $place = 9;

        foreach(Settings::$COBBLEX_DROP as $key => $data) {
            if($place >= 45)
                return;

            $item = clone $data["item"];

            if($item instanceof CustomItem)
                $item = $item->__toItem();

            $name = $item->hasCustomName() ? $item->getCustomName() : $item->getName();
            $this->setItem($place, $item->setCustomName("§e".$name . " §r§8(§e" . $data["chance"] . "§7%" . "§8)"), true, true);
            $place++;
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        if($slot === 49)
            $this->changeInventory($player, new MainDropInventory());

        $this->unClickItem($player);
        return true;
    }
}