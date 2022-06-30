<?php

namespace core\fakeinventory\inventory\upgrader;

use core\fakeinventory\FakeInventory;
use core\manager\managers\PacketManager;
use core\manager\managers\SoundManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\block\Air;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Tool;
use pocketmine\Player;

class AnvilInventory extends FakeInventory {

    private float $cost = 0.0;

    public function __construct(Player $player) {
        parent::__construct($player, "§l§9KOWADLO", self::SMALL);
        $this->setItems();
    }

    public function setItems() : void {
        $this->fillBars();

        $this->setItemAt(4, 2, Item::get(Item::BOTTLE_O_ENCHANTING)->setCustomName(" "));
        $this->setItemAt(5, 2, Item::get(Item::AIR));
        $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY MOC GO NAPRAWIC"));
        $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY MOC GO NAPRAWIC"));
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        $user = UserManager::getUser($player->getName());

        if($slot === 13) {

            if($sourceItem instanceof Tool || $sourceItem instanceof Armor) {
                $this->setItemAt(4, 2, Item::get(Item::BOTTLE_O_ENCHANTING)->setCustomName(" "));
                $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY MOC GO NAPRAWIC"));
                $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY MOC GO NAPRAWIC"));
                return false;
            }

            if(!$targetItem instanceof Tool && !$targetItem instanceof Armor && !$targetItem instanceof Air)
                return true;

            if($targetItem->getDamage() <= 0) {
                $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cTEN PRZEDMIOT JEST JUZ NAPRAWIONY"));
                $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cTEN PRZEDMIOT JEST JUZ NAPRAWIONY"));
                return false;
            }

            if($targetItem->getId() !== Item::AIR && $targetItem instanceof Armor || $targetItem instanceof Tool)
                $this->cost += ($targetItem->getDamage() / 350);

            $this->cost += ($this->cost / 10) * $targetItem->getBlockToolHarvestLevel();
            foreach($targetItem->getEnchantments() as $enchantment)
                $this->cost += ($enchantment->getLevel() / 10);

            $this->cost = (float) number_format(($user->hasSkill(4) ? ($this->cost / 2) : $this->cost), 2, '.', '');

            if($player->hasPermission(ConfigUtil::PERMISSION_TAG. "repair"))
                $this->cost = 0.00;

            $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 5)->setCustomName("§l§aKLIKNIJ ABY NAPRAWIC ITEM"));
            $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 5)->setCustomName("§l§aKLIKNIJ ABY NAPRAWIC ITEM"));
            $this->setItemAt(4, 2, Item::get(Item::BOTTLE_O_ENCHANTING)->setCustomName("§l§9INFORMACJE O NAPRAWIE" . "\n\n" .
                "§l§8» §r§7Cena: §l§9" . $this->cost . "§r§7zl!"));

            return false;
        }

        if($sourceItem->getId() === Item::CONCRETE) {
            if($sourceItem->getDamage() !== 5)
                return true;

            $item = $this->getItem(13);

            if(!$item instanceof Armor && !$item instanceof Tool)
                return true;

            if($user->getPlayerMoney() < $this->cost) {
                $this->closeFor($player);
                $player->sendMessage(MessageUtil::format("Nie maszy wystarczajaco duzo pieniedzy aby naprawic ten przedmiot! Brakuje ci: §l§9" . abs($user->getPlayerMoney() - $this->cost) . "§r§7zl"));
            }

            $user->reducePlayerMoney($this->cost);

            $this->setItemAt(4, 2, Item::get(Item::BOTTLE_O_ENCHANTING)->setCustomName(" "));
            $this->setItemAt(5, 2, Item::get(Item::AIR));
            $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY MOC GO NAPRAWIC"));
            $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY MOC GO NAPRAWIC"));
            $item->setDamage(0);

            $player->getInventory()->addItem($item);

        }

        PacketManager::unClickButton($player);
        return true;
    }

    public function onClose(Player $who) : void {

        $item = $this->getItem(13);

        if($item->getId() !== ItemIds::AIR)
            $who->getInventory()->addItem($item);

        parent::onClose($who);
    }
}