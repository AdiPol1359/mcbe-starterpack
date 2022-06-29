<?php

namespace core\fakeinventory\inventory\upgrader;

use core\fakeinventory\FakeInventory;
use core\manager\managers\PacketManager;
use core\manager\managers\SoundManager;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class BlackSmithInventory extends FakeInventory {

    public function __construct(Player $player) {
        parent::__construct($player, "§l§9KOWAL", self::SMALL);

        $this->setItems();
    }

    public function setItems() : void{
        $this->fillBars();

        $statTrackMenu = Item::get(ItemIds::NETHER_STAR);
        $statTrackMenu->setCustomName("§l§9STATTRACK"."\n\n".
            "§l§8» §r§7Zarzadzanie stattrackiem itemu");

        $repairMenu = Item::get(ItemIds::ANVIL);
        $repairMenu->setCustomName("§l§9KOWADLO"."\n\n".
            "§l§8» §r§7Naprawianie przedmiotu");

        $upgradeMenu = Item::get(ItemIds::BLAZE_POWDER);
        $upgradeMenu->setCustomName("§l§9UPGRADER"."\n\n".
            "§l§8» §r§7Upgrader itemu");

        $this->setItemAt(3, 2, $statTrackMenu);
        $this->setItemAt(5, 2, $repairMenu);
        $this->setItemAt(7, 2, $upgradeMenu);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        if($slot === 11)
            (new StatTrackInventory($player))->openFor([$player]);

        if($slot === 13)
            (new AnvilInventory($player))->openFor([$player]);

        if($slot === 15)
            (new UpgraderInventory($player))->openFor([$player]);

        PacketManager::unClickButton($player);
        return true;
    }
}