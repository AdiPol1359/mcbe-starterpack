<?php

namespace core\fakeinventory\inventory;

use core\fakeinventory\FakeInventory;
use core\manager\managers\PacketManager;
use core\manager\managers\SoundManager;
use core\user\UserManager;
use core\util\utils\InventoryUtil;
use core\util\utils\MessageUtil;
use core\util\utils\NumberUtil;
use pocketmine\block\Air;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\Player;

class EnchantInventory extends FakeInventory {

    private int $bookshelfCount;
    private array $ironBarsSlot = [];

    public function __construct(Player $player, int $bookshelfCount = 0) {
        parent::__construct($player, "§l§9ENCHANTY", self::BIG);
        $this->bookshelfCount = $bookshelfCount;
        $this->setItems();
    }

    public function setItems() : void {

        $this->fillBars();

        $bookShelf = Item::get(Item::BOOKSHELF)->setCustomName("§7Ilosc biblioteczek: §l§9" . $this->bookshelfCount);

        for($y = 1; $y <= 6; $y++)
            $this->setItemAt(1, $y, $bookShelf);

        for($y = 1; $y <= 6; $y++)
            $this->setItemAt(9, $y, $bookShelf);

        $this->setItemAt(5, 1, Item::get(Item::AIR));
        $this->setItemAt(4, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ZARZADZAC STATTRACKIEM"));
        $this->setItemAt(6, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ZARZADZAC STATTRACKIEM"));
        $this->setItemAt(5, 2, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ZARZADZAC STATTRACKIEM"));

        foreach($this->getContents(false) as $slot => $item) {
            if($item->getId() === Item::IRON_BARS)
                $this->ironBarsSlot[] = $slot;
        }

    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        $user = UserManager::getUser($player->getName());

        if($slot === 4) {

            if(count(InventoryUtil::getEnchantmentsForItem($sourceItem)) > 0) {

                foreach($this->ironBarsSlot as $slot)
                    $this->setItem($slot, Item::get(Item::IRON_BARS));

                $this->setItemAt(4, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY WYBRAC EMCHAN"));
                $this->setItemAt(6, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY WYBRAC EMCHAN"));
                $this->setItemAt(5, 2, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY WYBRAC EMCHANT"));
                return false;
            }

            if(count(InventoryUtil::getEnchantmentsForItem($targetItem)) <= 0 && !$targetItem instanceof Air)
                return true;

            $this->setItemAt(4, 1, Item::get(Item::CONCRETE, 5)->setCustomName("§l§aWYBIERZ INTERESUJACA CIE OPCJE"));
            $this->setItemAt(6, 1, Item::get(Item::CONCRETE, 5)->setCustomName("§l§aWYBIERZ INTERESUJACA CIE OPCJE"));
            $this->setItemAt(5, 2, Item::get(Item::CONCRETE, 5)->setCustomName("§l§aWYBIERZ INTERESUJACA CIE OPCJE"));

            $x = 2;
            $defaultY = 6;

            foreach(InventoryUtil::getEnchantmentsForItem($targetItem) as $enchantment => $maxLevel) {

                $level = $maxLevel;
                for($y = (($defaultY - $maxLevel) + 1); $y <= 6; $y++) {

                    $item = Item::get(Item::ENCHANTED_BOOK);
                    $item->setCustomName("§l§9" . $enchantment);
                    $item->setLore([
                        "\n",
                        "§l§8» §r§7Poziom: §l§9" . NumberUtil::numberToRoman($level) . "§r",
                        "§l§8» §r§7Koszt: §l§9" . ($user->hasSkill(3) ? (($level * 1.5) / 2) : $level * 1.5) . "§r§7zl",
                        "§l§8» §r§7Bliblioteczki: §l§9" . $level * 4
                    ]);

                    $item->getNamedTag()->setString("custom_enchant", $enchantment . ";" . $level . ";" . $level * 1.5 . ";" . $level * 4);

                    $this->setItemAt($x, $y, $item);
                    $level--;

                    if($y >= 6)
                        $x++;
                }
            }

            return false;
        }

        if($sourceItem->getId() === Item::ENCHANTED_BOOK) {

            $item = $this->getItem(4);

            if(count(InventoryUtil::getEnchantmentsForItem($item)) <= 0)
                return true;

            $namedTag = $sourceItem->getNamedTag();

            if(!$namedTag->hasTag("custom_enchant"))
                return true;

            $itemData = explode(";", $namedTag->getString("custom_enchant"));

            $enchantName = $itemData[0];
            $level = $itemData[1];
            $cost = ($user->hasSkill(3) ? ($itemData[2] / 2) : $itemData[2]);
            $bookshelf = $itemData[3];

            if($this->bookshelfCount < $bookshelf) {
                $this->closeFor($player);
                $player->sendMessage(MessageUtil::format("Brakuje ci §l§9" . abs($bookshelf - $this->bookshelfCount) . " §r§7biblioteczek aby moc zenchantowac przedmiot na ten poziom!"));
                return true;
            }

            if(!$player->isOp()) {
                if($user->getPlayerMoney() < $cost) {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Nie maszy wystarczajaco duzo pieniedzy aby nadac zaenchantowac na ten przedmiot! Brakuje ci §l§9" . abs($user->getPlayerMoney() - ($user->hasSkill(3) ? ($cost / 2) : $cost)) . "§r§7zl"));
                    return true;
                }

                $user->reducePlayerMoney($cost);
            }

            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantmentByName($enchantName), $level));
            $this->setItemAt(5, 1, $item);
        }

        PacketManager::unClickButton($player);
        return true;
    }

    public function onClose(Player $who) : void {

        $item = $this->getItem(4);

        if($item->getId() !== Item::AIR)
            $who->getInventory()->addItem($item);

        parent::onClose($who);
    }
}