<?php

namespace core\fakeinventory\inventory;

use core\fakeinventory\FakeInventory;
use core\manager\managers\PacketManager;
use core\manager\managers\SoundManager;
use core\user\UserManager;
use core\util\utils\ItemUtil;
use pocketmine\item\Item;
use pocketmine\Player;

class DropInventory extends FakeInventory{

    public function __construct(Player $player) {
        parent::__construct($player, "§l§9DROP", self::BIG);
        $this->setItems();
    }

    public function setItems() : void{

        $this->fillBars();

        $money = $this->dropStatus("money", Item::get(Item::GOLD_NUGGET)->setCustomName("§r§l§7[§8---===§7[ §r§l§9PIENIADZE§r§7§l ]§8===---§7]")->setLore([
            " ",
            "§r§8» §7Status: ",
            "§r§8» §7Szanse: §8(§b2.50%§r§8)",
            "§r§8» §7Kazdy poziom fortuny: §8+(§b1 drop§r§8)",
            "§r§8» §7Ranga sponsor: §8+(§b0.30%§r§8)"
        ]));

        $iron = $this->dropStatus("iron", Item::get(Item::IRON_INGOT)->setCustomName("§r§l§7[§8---===§7[ §r§l§9ZELAZO§r§7§l ]§8===---§7]")->setLore([
            " ",
            "§r§8» §7Status: ",
            "§r§8» §7Szanse: §8(§b2.50%§r§8)",
            "§r§8» §7Kazdy poziom fortuny: §8+(§b1 drop§r§8)",
            "§r§8» §7Ranga sponsor: §8+(§b0.30%§r§8)"
        ]));


        $gold = $this->dropStatus("gold", Item::get(Item::GOLD_INGOT)->setCustomName("§r§l§7[§8---===§7[ §r§l§9ZLOTO§r§7§l ]§8===---§7]")->setLore([
            " ",
            "§r§8» §7Status: ",
            "§r§8» §7Szanse: §8(§b3.00%§r§8)",
            "§r§8» §7Kazdy poziom fortuny: §8+(§b1 drop§r§8)",
            "§r§8» §7Ranga sponsor: §8+(§b0.30%§r§8)"
        ]));

        $coal = $this->dropStatus("coal", Item::get(Item::COAL)->setCustomName("§r§l§7[§8---===§7[ §r§l§9WEGIEL§r§7§l ]§8===---§7]")->setLore([
            " ",
            "§r§8» §7Status: ",
            "§r§8» §7Szanse: §8(§b4.00%§r§8)",
            "§r§8» §7Kazdy poziom fortuny: §8+(§b1 drop§r§8)",
            "§r§8» §7Ranga sponsor: §8+(§b0.30%§r§8)"
        ]));


        $diamond = $this->dropStatus("diamond", Item::get(Item::DIAMOND)->setCustomName("§r§l§7[§8---===§7[ §r§l§9DIAMENTY§r§7§l ]§8===---§7]")->setLore([
            " ",
            "§r§8» §7Status: ",
            "§r§8» §7Szanse: §8(§b1.50%§r§8)",
            "§r§8» §7Kazdy poziom fortuny: §8+(§b1 drop§r§8)",
            "§r§8» §7Ranga sponsor: §8+(§b0.30%§r§8)"
        ]));


        $emerald = $this->dropStatus("emerald", Item::get(Item::EMERALD)->setCustomName("§r§l§7[§8---===§7[ §r§l§9EMERALDY§r§7§l ]§8===---§7]")->setLore([
            " ",
            "§r§8» §7Status: ",
            "§r§8» §7Szanse: §8(§b2.00%§r§8)",
            "§r§8» §7Kazdy poziom fortuny: §8+(§b1 drop§r§8)",
            "§r§8» §7Ranga sponsor: §8+(§b0.30%§r§8)"
        ]));


        $cobblestone = $this->dropStatus("cobble", Item::get(Item::COBBLESTONE)->setCustomName("§r§l§7[§8---===§7[ §r§l§9COBBLESTONE§r§7§l ]§8===---§7]")->setLore([
            " ",
            "§r§8» §7Status: ",
            "§r§8» §7Szanse: §8(§b100.0%§r§8)",
            "§r§8» §7Kazdy poziom fortuny: §8+(§b1 drop§r§8)",
            "§r§8» §7Ranga sponsor: §8+(§b0.30%§r§8)"
        ]));

        $this->setItem(31, $money);
        $this->setItem(11, $iron);
        $this->setItem(15, $gold);
        $this->setItem(10, $coal);
        $this->setItem(21, $diamond);
        $this->setItem(23, $emerald);
        $this->setItem(16, $cobblestone);

        $lime = Item::get(236, 5)->setCustomName("§l§aWLACZ WSZYSTKO");
        $red = Item::get(236, 14)->setCustomName("§l§cWYLACZ WSZYSTKO");

        $this->setItem(37, $lime);
        $this->setItem(38, $red);

    }

    private function dropStatus(string $name, Item $item) : Item {
        $user = UserManager::getUser($this->player->getName());

        if(!isset($item->getLore()[4]))
            return $item;

        if($user->isDropEnabled($name)){
            ItemUtil::addItemGlow($item);
            $item->setLore([$item->getLore()[0], $item->getLore()[1]."§aWLACZONY", $item->getLore()[2], $item->getLore()[3], $item->getLore()[4]]);
        }else
            $item->setLore([$item->getLore()[0], $item->getLore()[1]."§cWYLACZONY", $item->getLore()[2], $item->getLore()[3], $item->getLore()[4]]);

        return $item;
    }
    
    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");


        $user = UserManager::getUser($player->getName());

        if($sourceItem->getId() === Item::COAL)
            $user->switchDrop("coal");

        if($sourceItem->getId() === Item::IRON_INGOT)
            $user->switchDrop("iron");

        if($sourceItem->getId() === Item::DIAMOND)
            $user->switchDrop("diamond");

        if($sourceItem->getId() === Item::GOLD_NUGGET)
            $user->switchDrop("money");

        if($sourceItem->getId() === Item::EMERALD)
            $user->switchDrop("emerald");

        if($sourceItem->getId() === Item::GOLD_INGOT)
            $user->switchDrop("gold");

        if($sourceItem->getId() === Item::COBBLESTONE)
            $user->switchDrop("cobble");

        if($sourceItem->getId() === Item::CONCRETE){
            if($sourceItem->getDamage() === 5) {
                $user->setDrop("coal");
                $user->switchDrop("iron");
                $user->switchDrop("diamond");
                $user->switchDrop("money");
                $user->switchDrop("emerald");
                $user->switchDrop("gold");
                $user->switchDrop("cobble");
            }

            if($sourceItem->getDamage() === 14) {
                $user->setDrop("coal", 0);
                $user->setDrop("iron", 0);
                $user->setDrop("diamond", 0);
                $user->setDrop("money", 0);
                $user->setDrop("emerald", 0);
                $user->setDrop("gold", 0);
                $user->setDrop("cobble", 0);
            }
        }

        $this->setItems();
        PacketManager::unClickButton($player);

        return true;
    }
}