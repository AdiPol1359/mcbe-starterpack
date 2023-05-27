<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\guild;

use core\inventories\FakeInventory;
use core\utils\Settings;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class ItemsInventory extends FakeInventory {

    public function __construct(private Player $player) {
        parent::__construct("§l§eITEMY NA GILDIE");
    }

    public function setItems() : void{
        $itemFactory = ItemFactory::getInstance();
        $this->fill();

        foreach(Settings::$GUILD_ITEMS as $slot => $item){
            $clonedItem = clone $item;

            $red = $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName(" ");
            $green = $itemFactory->get(ItemIds::CONCRETE, 5)->setCustomName(" ");

            if($this->player->getInventory()->contains($clonedItem)) {
                $clonedItem->setCustomName("§l§aPOSIADANE");
                $this->setItem(($slot - 9), $green, true, true);
                $this->setItem(($slot + 9), $green, true, true);
            }else {
                $clonedItem->setCustomName("§l§cNIE POSIADANE");
                $this->setItem(($slot - 9), $red, true, true);
                $this->setItem(($slot + 9), $red, true, true);
            }

            $this->setItem($slot, $clonedItem, true, true);
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $this->unClickItem($player);
        return true;
    }
}