<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\invsee;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryPatterns;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class ChoosePlayerInventory extends FakeInventory {

    private string $targetPlayer;

    public function __construct(string $targetPlayer) {
        $this->targetPlayer = $targetPlayer;

        parent::__construct("§8Ekwipunek §e".$targetPlayer);
    }

    public function setItems() : void {
        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_CORNERS_SMALL);
        $itemFactory = ItemFactory::getInstance();

        $this->setItem(11, $itemFactory->get(ItemIds::CHEST)->setCustomName("§l§eEKWIPUNEK"), true, true);
        $this->setItem(13, $itemFactory->get(ItemIds::DIAMOND_CHESTPLATE)->setCustomName("§l§eARMOR"), true, true);
        $this->setItem(15, $itemFactory->get(ItemIds::ENDER_CHEST)->setCustomName("§l§eENDERCHEST"), true, true);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() === ItemIds::CHEST)
            $this->changeInventory($player, new InventoryInvsee($this->targetPlayer));

        if($sourceItem->getId() === ItemIds::DIAMOND_CHESTPLATE)
            $this->changeInventory($player, new ArmorInvsee($this->targetPlayer));

        if($sourceItem->getId() === ItemIds::ENDER_CHEST)
            $this->changeInventory($player, new EnderChestInvsee($this->targetPlayer));

        $this->unClickItem($player);
        return true;
    }
}