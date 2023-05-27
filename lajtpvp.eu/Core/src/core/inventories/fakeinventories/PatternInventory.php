<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\inventories\FakeInventorySize;
use core\items\custom\Safe;
use core\Main;
use pocketmine\block\BlockFactory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class PatternInventory extends FakeInventory {

    public function __construct() {
        parent::__construct("§l§ePATTERN", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void {
        $blockFactory = BlockFactory::getInstance();
        $itemFactory = ItemFactory::getInstance();
        $this->fill();

        $this->setItem(10, $itemFactory->get(ItemIds::SHULKER_BOX, 14)->setCustomName(" "));
        $this->setItem(11, $itemFactory->get(ItemIds::SHULKER_BOX, 1)->setCustomName(" "));
        $this->setItem(12, $itemFactory->get(ItemIds::SHULKER_BOX, 4)->setCustomName(" "));
        $this->setItem(13, $itemFactory->get(ItemIds::SHULKER_BOX, 5)->setCustomName(" "));
        $this->setItem(14, $itemFactory->get(ItemIds::SHULKER_BOX, 13)->setCustomName(" "));
        $this->setItem(15, $itemFactory->get(ItemIds::SHULKER_BOX, 3)->setCustomName(" "));
        $this->setItem(16, $itemFactory->get(ItemIds::SHULKER_BOX, 9)->setCustomName(" "));
        $this->setItem(19, $itemFactory->get(ItemIds::SHULKER_BOX, 11)->setCustomName(" "));
        $this->setItem(20, $itemFactory->get(ItemIds::SHULKER_BOX, 10)->setCustomName(" "));
        $this->setItem(21, $itemFactory->get(ItemIds::UNDYED_SHULKER_BOX)->setCustomName(" "));
        $this->setItem(22, $itemFactory->get(ItemIds::SHULKER_BOX, 6)->setCustomName(" "));
        $this->setItem(23, $itemFactory->get(ItemIds::SHULKER_BOX, 2)->setCustomName(" "));
        $this->setItem(24, $itemFactory->get(ItemIds::SHULKER_BOX, 12)->setCustomName(" "));
        $this->setItem(25, $itemFactory->get(ItemIds::SHULKER_BOX, 0)->setCustomName(" "));
        $this->setItem(28, $itemFactory->get(ItemIds::SHULKER_BOX, 8)->setCustomName(" "));
        $this->setItem(29, $itemFactory->get(ItemIds::SHULKER_BOX, 7)->setCustomName(" "));

        $this->setItem(30, $itemFactory->get(ItemIds::CHEST)->setCustomName(" "));

        $this->setItem(31, $itemFactory->get(ItemIds::ENDER_CHEST)->setCustomName(" "));
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== ItemIds::STAINED_GLASS_PANE) {
            $item = $player->getInventory()->getItemInHand();

            if(Main::getInstance()->getSafeManager()->isSafe($item)) {
                $safe = Main::getInstance()->getSafeManager()->getSafeById($item->getNamedTag()->getInt("safeId"));

                if($safe->getName() === $player->getName()) {
                    $safe->setPattern($sourceItem);

                    $item = (new Safe($safe))->__toItem();
                    $player->getInventory()->setItemInHand($item);
                }
            }
        }

        $this->unClickItem($player);
        return true;
    }
}