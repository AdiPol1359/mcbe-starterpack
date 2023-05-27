<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryPatterns;
use core\inventories\FakeInventorySize;
use core\Main;
use core\utils\LoreCreator;
use core\utils\PermissionUtil;
use core\utils\Settings;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class BackpackInventory extends FakeInventory {

    public function __construct(private Player $player) {
        parent::__construct("§l§ePLECAK", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void {

        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_CORNERS);

        if(($user = Main::getInstance()->getUserManager()->getUser($this->player->getName())) === null) {
            return;
        }

        $diamond = VanillaItems::DIAMOND();
        $this->setInfo($diamond, "diamond");
        $this->setItem(20, $diamond, true, true);

        $emerald = VanillaItems::EMERALD();
        $this->setInfo($emerald, "emerald");
        $this->setItem(21, $emerald, true, true);

        $gold = VanillaItems::GOLD_INGOT();
        $this->setInfo($gold, "gold");
        $this->setItem(22, $gold, true, true);

        $iron = VanillaItems::IRON_INGOT();
        $this->setInfo($iron, "iron");
        $this->setItem(23, $iron, true, true);

        $coal = VanillaItems::COAL();
        $this->setInfo($coal, "coal");
        $this->setItem(24, $coal, true, true);

        $book = VanillaItems::BOOK();
        $this->setInfo($book, "book");
        $this->setItem(29, $book, true, true);

        $apple = VanillaItems::APPLE();
        $this->setInfo($apple, "apple");
        $this->setItem(30, $apple, true, true);

        $tnt = VanillaBlocks::TNT()->asItem();
        $this->setInfo($tnt, "tnt");
        $this->setItem(31, $tnt, true, true);

        $obsidian = VanillaBlocks::OBSIDIAN()->asItem();
        $this->setInfo($obsidian, "obsidian");
        $this->setItem(32, $obsidian, true, true);

        $hopper = VanillaBlocks::HOPPER()->asItem();
        $hopper->setCustomName("§7[§8----====§7[ §ePLECAK§r§7 ]§8====----§7]");

        $hopper->setLore([
            "",
            "§r§7Pojemnosc§8: §e".$user->getBackpackManager()->getItemsCountInBackpack()."§8/§e".(PermissionUtil::has($this->player, Settings::$PERMISSION_TAG."backpack.unlimited") ? "NIELIMITOWANY" : $user->getBackpackManager()->getMaxBackpackSize()),
            "§r§7Ulepszenie §8(§7emeraldy§8)",
            "§r§8x§e".Settings::$COST_UPGRADE_SIZE." §8(§7bezzmiennie§8)",
            ""
        ]);

        $loreCreator = new LoreCreator($hopper->getCustomName(), $hopper->getLore());
        $loreCreator->alignLore();
        $hopper->setLore($loreCreator->getLore());
        $this->setItem(49, $hopper, true, true);
    }

    private function setInfo(Item $item, string $name) : void {
        $namedTag = $item->getNamedTag();
        $namedTag->setString("dropName", $name);

        if(($user = Main::getInstance()->getUserManager()->getUser($this->player->getName())) === null)
            return;

        $item->setCustomName("§e".$item->getName()." §8(§7x§e".$user->getBackpackManager()->getItemCount(Main::getInstance()->getDropManager()->getDropByName($name))."§8)");
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $namedTag = $sourceItem->getNamedTag();
        $user = Main::getInstance()->getUserManager()->getUser($this->player->getName());

        if($user) {
            if($namedTag->getString("dropName", "null") !== "null") {
                $dropName = $namedTag->getString("dropName");
                $dropData = Main::getInstance()->getDropManager()->getDropByName($dropName);
                $dropInfo = $user->getBackpackManager()->getItemCount($dropData);

                if($dropInfo > 0) {
                    if($dropInfo >= 64) {
                        $player->getInventory()->addItem(ItemFactory::getInstance()->get($sourceItem->getId(), $sourceItem->getMeta(), 64));
                        $user->getBackpackManager()->reduceItem($dropData, 64);
                    } else {
                        $player->getInventory()->addItem(ItemFactory::getInstance()->get($sourceItem->getId(), $sourceItem->getMeta()));
                        $user->getBackpackManager()->reduceItem($dropData);
                    }

                    $this->setItems();
                }
            }

            if($sourceItem->getId() === ItemIds::HOPPER) {

                $containItem = ItemFactory::getInstance()->get(ItemIds::EMERALD, 0, Settings::$COST_UPGRADE_SIZE);

                if($player->getInventory()->contains($containItem)) {
                    $player->getInventory()->removeItem($containItem);
                    $user->getBackpackManager()->setBackpackSize($user->getBackpackManager()->getMaxBackpackSize() + Settings::$UPGRADE_BACKPACK_SIZE);
                    $this->setItems();
                }
            }
        }

        $this->unClickItem($player);
        return true;
    }
}