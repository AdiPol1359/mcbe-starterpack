<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\inventories\FakeInventorySize;
use core\Main;
use core\managers\ServerManager;
use core\utils\InventoryUtil;
use core\utils\ItemUtil;
use core\utils\MessageUtil;
use core\utils\NumberUtil;
use core\utils\SoundUtil;
use pocketmine\block\Air;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\Sword;
use pocketmine\item\Tool;
use pocketmine\player\Player;

class EnchantInventory extends FakeInventory {

    private int $bookshelfCount;
    private array $ironBarsSlot = [];

    public function __construct(int $bookshelfCount = 0) {
        $this->bookshelfCount = $bookshelfCount;

        parent::__construct("§l§eENCHANTY", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void {
        $itemFactory = ItemFactory::getInstance();
        $this->fill();

        $bookShelf = VanillaBlocks::BOOKSHELF()->asItem()->setCustomName("§7Ilosc biblioteczek: §l§e" . $this->bookshelfCount);

        for($y = 1; $y <= 6; $y++)
            $this->setItemAt(1, $y, $bookShelf);

        for($y = 1; $y <= 6; $y++)
            $this->setItemAt(9, $y, $bookShelf);

        $this->setItemAt(5, 1, ItemFactory::air());
        $this->setItemAt(4, 1, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ENCHANTOWAC"));
        $this->setItemAt(6, 1, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ENCHANTOWAC"));
        $this->setItemAt(5, 2, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ENCHANTOWAC"));

        foreach($this->getContents(false) as $slot => $item) {
            if($item->getId() === ItemIds::STAINED_GLASS_PANE)
                $this->ironBarsSlot[] = $slot;
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        $itemFactory = ItemFactory::getInstance();

        if($sourceItem->getId() !== ItemIds::STAINED_GLASS_PANE)
            SoundUtil::addSound([$player], $this->holder, "random.click");

        if($slot === 4) {

            if(count(InventoryUtil::getEnchantmentsForItem($sourceItem)) > 0) {

                foreach($this->ironBarsSlot as $slot)
                    $this->setItem($slot, $itemFactory->get(ItemIds::STAINED_GLASS_PANE, 7), true, true);

                $this->setItemAt(4, 1, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ENCHANTOWAC"));
                $this->setItemAt(6, 1, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ENCHANTOWAC"));
                $this->setItemAt(5, 2, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ENCHANTOWAC"));
                return false;
            }

            if(count(InventoryUtil::getEnchantmentsForItem($targetItem)) <= 0)
                return true;

            $this->setItemAt(4, 1, $itemFactory->get(ItemIds::CONCRETE, 5)->setCustomName("§l§aWYBIERZ INTERESUJACA CIE OPCJE"));
            $this->setItemAt(6, 1, $itemFactory->get(ItemIds::CONCRETE, 5)->setCustomName("§l§aWYBIERZ INTERESUJACA CIE OPCJE"));
            $this->setItemAt(5, 2, $itemFactory->get(ItemIds::CONCRETE, 5)->setCustomName("§l§aWYBIERZ INTERESUJACA CIE OPCJE"));

            $x = 2;
            $defaultY = 6;

            foreach(InventoryUtil::getEnchantmentsForItem($targetItem) as $enchantment => $maxLevel) {

                $level = $maxLevel;
                for($y = (($defaultY - $maxLevel) + 1); $y <= 6; $y++) {

                    $item = $itemFactory->get(ItemIds::ENCHANTED_BOOK);
                    $item->setCustomName("§l§e" . $enchantment);
                    $item->setLore([
                        "\n",
                        "§l§8» §r§7Poziom: §l§e" . NumberUtil::numberToRoman($level) . "§r",
                        "§l§8» §r§7Koszt: §l§e" . $level * 6 . "§r§7lvl",
                        "§l§8» §r§7Bliblioteczki: §l§e" . $level * 4
                    ]);

                    $item->getNamedTag()->setString("custom_enchant", $enchantment . ";" . $level . ";" . $level * 6 . ";" . $level * 4);

                    $this->setItemAt($x, $y, $item);
                    $level--;

                    if($y >= 6)
                        $x++;
                }
            }

            return false;
        }


        if($sourceItem->getId() === ItemIds::ENCHANTED_BOOK) {

            $item = $this->getItem(4);

            if(!$item instanceof Tool || $item instanceof Sword) {
                if(!Main::getInstance()->getServerManager()->isSettingEnabled(ServerManager::ENCHANTS)) {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Enchanty sa wylaczone!"));
                    return true;
                }
            }

            if(count(InventoryUtil::getEnchantmentsForItem($item)) <= 0)
                return true;

            $namedTag = $sourceItem->getNamedTag();

            if(!$namedTag->getTag("custom_enchant"))
                return true;

            $itemData = explode(";", $namedTag->getString("custom_enchant"));

            $enchantName = $itemData[0];
            $level = (int)$itemData[1];
            $cost = $itemData[2];
            $bookshelf = $itemData[3];

            if($this->bookshelfCount < $bookshelf) {
                $this->closeFor($player);
                $player->sendMessage(MessageUtil::format("Brakuje ci §l§e" . abs($bookshelf - $this->bookshelfCount) . " §r§7biblioteczek aby moc zenchantowac przedmiot na ten poziom!"));
                return true;
            }

            $xpManager = $player->getXpManager();
            if(!$player->getServer()->isOp($player->getName())) {
                if($xpManager->getXpLevel() < $cost) {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Nie maszy wystarczajaco duzo lvla aby zaenchantoewac ten przedmiot! Brakuje ci §l§e" . abs($xpManager->getXpLevel() - $cost) . "§r§7lvl"));
                    return true;
                }

                $xpManager->setXpLevel($xpManager->getXpLevel() - $cost);
            }

            $item->addEnchantment(new EnchantmentInstance(StringToEnchantmentParser::getInstance()->parse($enchantName), $level));
            $this->setItemAt(5, 1, $item);
        }

        $this->unClickItem($player);
        return true;
    }

    public function onClose(Player $who) : void {
        $item = $this->getItem(4);

        if(ItemUtil::isRepairable($item))
            $who->getInventory()->addItem($item);

        $this->setItem(4, ItemFactory::air(), true, true);

        parent::onClose($who);
    }
}