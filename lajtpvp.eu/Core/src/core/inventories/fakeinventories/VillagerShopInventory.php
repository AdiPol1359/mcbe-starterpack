<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\managers\villager\VillagerShop;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class VillagerShopInventory extends FakeInventory {

    public function __construct(private VillagerShop|null $villager) {
        parent::__construct( $villager->getName());
    }

    public function setItems() : void {

        if(!($villager = $this->villager))
            return;

        foreach($villager->getItems() as $slot => $item) {
            if($slot >= $this->getSize())
                continue;

            $clonedItem = clone $item;

            $namedTag = $clonedItem->getNamedTag();
            $namedTag->setString("savedName", $clonedItem->getCustomName());

            $clonedItem->setCustomName("§e".$clonedItem->getName()."\n§8(§7x§a".$namedTag->getInt("costItem")." emeraldow§8)\n§8(kliknij aby kupic)");
            $this->setItem($slot, $clonedItem, true, true);
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $namedTag = $sourceItem->getNamedTag();

        if($namedTag->getTag("costItem") && $namedTag->getTag("savedName")) {
            $cost = $namedTag->getInt("costItem");
            $normalName = $namedTag->getString("savedName");

            $item = ItemFactory::getInstance()->get(ItemIds::EMERALD, 0, $cost);

            $receiveItem = clone $sourceItem;
            $receiveItemNamedTag = $receiveItem->getNamedTag();
            $receiveItem->setCustomName($normalName);

            foreach(["costItem", "savedName", "shopSlot"] as $tag)
                $receiveItemNamedTag->removeTag($tag);

            if($player->getInventory()->contains($item)) {
                $player->getInventory()->removeItem($item);
                $player->getInventory()->addItem($receiveItem);
            }
        }

        $this->unClickItem($player);
        return true;
    }
}