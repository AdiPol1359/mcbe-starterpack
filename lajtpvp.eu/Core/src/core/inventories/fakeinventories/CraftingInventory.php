<?php

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\inventories\FakeInventorySize;
use core\items\custom\BoyFarmer;
use core\items\custom\CobbleX;
use core\items\custom\FosMiner;
use core\items\custom\StoneGenerator;
use core\items\custom\ThrownTNT;
use core\utils\InventoryUtil;
use core\utils\ItemUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class CraftingInventory extends FakeInventory {

    /** @var Item[] */
    private array $items = [];

    private string $block = "";

    public function __construct() {
        parent::__construct("§l§eCRAFTINGI", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void {
        $this->fill();

        $stone = (new FosMiner())->__toItem();
        $this->setItemAt(1, 2, $stone);

        $endStone = (new StoneGenerator())->__toItem();
        $this->setItemAt(1, 3, $endStone);

        $obsidian = (new BoyFarmer())->__toItem();
        $this->setItemAt(1, 4, $obsidian);

        $tnt = (new ThrownTNT())->__toItem();
        $this->setItemAt(9, 2, $tnt);

        $mossy = (new CobbleX())->__toItem();
        $this->setItemAt(9, 3, $mossy);

        $enderChest = VanillaBlocks::ENDER_CHEST()->asItem();
        $enderChest->setCustomName("§l§eENDERCHEST");
        ItemUtil::addItemGlow($enderChest);

        $this->setItemAt(9, 4, $enderChest);

        $air = VanillaBlocks::AIR()->asItem();
        $airSlots = [12, 13, 14, 21, 22, 23, 30, 31, 32];

        foreach($airSlots as $slot)
            $this->setItem($slot, $air);

        $this->setItem(39, ItemFactory::getInstance()->get(ItemIds::CONCRETE, 14)->setCustomName("§l§cWYBIERZ CRAFTING"), true, true);
        $this->setItem(41, ItemFactory::getInstance()->get(ItemIds::CONCRETE, 1)->setCustomName("§l§6CRAFTUJ WSZYSTKO"), true, true);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $disableSlots = [12, 13, 14, 21, 22, 23, 30, 31, 32];

        if(in_array($slot, $disableSlots)) {
            $this->unClickItem($player);
            return true;
        }

        $meta = 14;
        $name = "§l§cWYBIERZ CRAFTING";

        switch($sourceItem->getId()) {
            case ItemIds::STONE:

                $this->items[12] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[13] = VanillaItems::DIAMOND();
                $this->items[14] = VanillaBlocks::OBSIDIAN()->asItem();

                $this->items[21] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[22] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[23] = VanillaBlocks::OBSIDIAN()->asItem();

                $this->items[30] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[31] = VanillaItems::DIAMOND();
                $this->items[32] = VanillaBlocks::OBSIDIAN()->asItem();

                $meta = 5;
                $name = "§r§l§aCRAFTUJ KOPACZ FOS";
                $this->block = "KOPACZ FOS";
                break;

            case ItemIds::END_STONE:

                $this->items[12] = VanillaBlocks::STONE()->asItem();
                $this->items[13] = VanillaBlocks::STONE()->asItem();
                $this->items[14] = VanillaBlocks::STONE()->asItem();

                $this->items[21] = VanillaBlocks::STONE()->asItem();
                $this->items[22] = VanillaItems::DIAMOND();
                $this->items[23] = VanillaBlocks::STONE()->asItem();

                $this->items[30] = VanillaBlocks::STONE()->asItem();
                $this->items[31] = VanillaBlocks::STONE()->asItem();
                $this->items[32] = VanillaBlocks::STONE()->asItem();

                $meta = 5;
                $name = "§r§l§aCRAFTUJ STONIARKA";
                $this->block = "STONIARKA";

                break;

            case ItemIds::OBSIDIAN:

                $this->items[12] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[13] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[14] = VanillaBlocks::OBSIDIAN()->asItem();

                $this->items[21] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[22] = VanillaItems::DIAMOND();
                $this->items[23] = VanillaBlocks::OBSIDIAN()->asItem();

                $this->items[30] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[31] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[32] = VanillaBlocks::OBSIDIAN()->asItem();

                $meta = 5;
                $name = "§r§l§aCRAFTUJ BOYFARMER";
                $this->block = "BOYFARMER";

                break;

            case ItemIds::SAND:

                $this->items[12] = VanillaBlocks::SAND()->asItem();
                $this->items[13] = VanillaBlocks::SAND()->asItem();
                $this->items[14] = VanillaBlocks::SAND()->asItem();

                $this->items[21] = VanillaBlocks::SAND()->asItem();
                $this->items[22] = VanillaItems::DIAMOND();
                $this->items[23] = VanillaBlocks::SAND()->asItem();

                $this->items[30] = VanillaBlocks::SAND()->asItem();
                $this->items[31] = VanillaBlocks::SAND()->asItem();
                $this->items[32] = VanillaBlocks::SAND()->asItem();

                $meta = 5;
                $name = "§r§l§aCRAFTUJ SANDFARMER";
                $this->block = "SANDFARMER";

                break;

            case ItemIds::MOB_SPAWNER:

                $this->items[12] = VanillaBlocks::TNT()->asItem()->setCount(64);
                $this->items[13] = VanillaBlocks::TNT()->asItem()->setCount(64);
                $this->items[14] = VanillaBlocks::TNT()->asItem()->setCount(64);

                $this->items[21] = VanillaBlocks::TNT()->asItem()->setCount(64);
                $this->items[22] = VanillaBlocks::TNT()->asItem()->setCount(64);
                $this->items[23] = VanillaBlocks::TNT()->asItem()->setCount(64);

                $this->items[30] = VanillaBlocks::TNT()->asItem()->setCount(64);
                $this->items[31] = VanillaBlocks::TNT()->asItem()->setCount(64);
                $this->items[32] = VanillaBlocks::TNT()->asItem()->setCount(64);

                $meta = 5;
                $name = "§r§l§aCRAFTUJ RZUCAK";
                $this->block = "RZUCAK";

                break;

            case ItemIds::MOSSY_COBBLESTONE:

                $this->items[12] = VanillaBlocks::COBBLESTONE()->asItem()->setCount(64);
                $this->items[13] = VanillaBlocks::COBBLESTONE()->asItem()->setCount(64);
                $this->items[14] = VanillaBlocks::COBBLESTONE()->asItem()->setCount(64);

                $this->items[21] = VanillaBlocks::COBBLESTONE()->asItem()->setCount(64);
                $this->items[22] = VanillaBlocks::COBBLESTONE()->asItem()->setCount(64);
                $this->items[23] = VanillaBlocks::COBBLESTONE()->asItem()->setCount(64);

                $this->items[30] = VanillaBlocks::COBBLESTONE()->asItem()->setCount(64);
                $this->items[31] = VanillaBlocks::COBBLESTONE()->asItem()->setCount(64);
                $this->items[32] = VanillaBlocks::COBBLESTONE()->asItem()->setCount(64);

                $meta = 5;
                $name = "§r§l§aCRAFTUJ COBBLEX";
                $this->block = "COBBLEX";

                break;

            case ItemIds::ENDER_CHEST:

                $this->items[12] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[13] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[14] = VanillaBlocks::OBSIDIAN()->asItem();

                $this->items[21] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[22] = VanillaItems::ENDER_PEARL();
                $this->items[23] = VanillaBlocks::OBSIDIAN()->asItem();

                $this->items[30] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[31] = VanillaBlocks::OBSIDIAN()->asItem();
                $this->items[32] = VanillaBlocks::OBSIDIAN()->asItem();

                $meta = 5;
                $name = "§r§l§aCRAFTUJ ENDER CHEST";
                $this->block = "ENDER CHEST";

                break;
        }

        if($slot === 41 || $slot === 39) {

            if(!empty($this->items)) {

                /** @var Item[] $containsItems */
                $containsItems = [];
                $continue = true;

                foreach($this->items as $itemSlot => $item) {
                    if(isset($containsItems[$item->getId()]))
                        $containsItems[$item->getId()] = ($containsItems[$item->getId()]->setCount(($containsItems[$item->getId()]->getCount() + $item->getCount())));
                    else
                        $containsItems[$item->getId()] = clone $item;
                }

                $defaultData = [];

                for($i = 1, $stop = false, $test = true; true; $i++) {

                    foreach($containsItems as $key => $itemData){
                        if(!isset($defaultData[$itemData->getId()]))
                            $defaultData[$itemData->getId()] = clone $containsItems[$itemData->getId()];

                        $itemData->setCount(($itemData->getCount() + ($i > 1 ? ($defaultData[$itemData->getId()]->getCount()) : 0)));

                        if($i > 1)
                            $test = false;
                    }

                    if(!InventoryUtil::containItems($player, $containsItems)){
                        if($test)
                            $stop = true;

                        break;
                    }
                }

                $i--;

                if($stop){
                    $meta = 14;
                    $name = "§r§l§cNIE MASZ WYSTARCZAJACO DUZO ITEMOW";
                    $continue = false;
                }

                if($continue){

                    foreach($defaultData as $itemId => $itemData)
                        InventoryUtil::removeItem($player, ($itemData->setCount(($slot === 39 ? $itemData->getCount() : $itemData->getCount() * $i))));

                    $meta = 5;
                    $name = "§r§l§aCRAFTUJ ".$this->block;

                    $dropItem = null;

                    $dropItem = match ($this->block) {
                        "STONIARKA" => (new StoneGenerator())->__toItem(),
                        "KOPACZ FOS" => (new FosMiner())->__toItem(),
                        "BOYFARMER" => (new BoyFarmer())->__toItem(),
                        "RZUCAK" => (new ThrownTNT())->__toItem(),
                        "ENDER CHEST" => VanillaBlocks::ENDER_CHEST(),
                        "COBBLEX" => (new CobbleX())->__toItem(),
                    };

                    if($dropItem !== null)
                        InventoryUtil::addItem(($dropItem->setCount(($slot === 39 ? 1 : $i))), $player);
                }
            }
        }

        $confirmItem = ItemFactory::getInstance()->get(ItemIds::CONCRETE, $meta);
        $confirmItem->setCustomName($name);

        $this->setItem(39, $confirmItem, true, true);

        foreach($this->items as $slot => $item)
            $this->setItem($slot, $item, true, true);

        $this->unClickItem($player);
        return true;
    }
}