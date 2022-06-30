<?php

namespace core\fakeinventory\inventory;

use core\fakeinventory\FakeInventory;
use core\manager\managers\PacketManager;
use core\manager\managers\ServerManager;
use pocketmine\item\Item;
use pocketmine\Player;

class ServerInventory extends FakeInventory {

    public function __construct(Player $player) {
        parent::__construct($player, "§l§9SERVER", self::SMALL);

        $this->setItems();
    }

    public function setItems() : void {

        $this->fillBars();

        $nametag = Item::get(Item::NAMETAG);
        ServerManager::isSettingEnabled(ServerManager::ITEMSHOP) ? $nametag->setCustomName("§aITEMSHOP") : $nametag->setCustomName("§cITEMSHOP");

        $hazard = Item::get(Item::NETHER_STAR);
        ServerManager::isSettingEnabled(ServerManager::HAZARD) ? $hazard->setCustomName("§aHAZARD") : $hazard->setCustomName("§cHAZARD");

        $paper = Item::get(Item::PAPER);
        ServerManager::isSettingEnabled(ServerManager::SHOP) ? $paper->setCustomName("§aSKLEP") : $paper->setCustomName("§cSKLEP");

        $this->setItem(10, $nametag);
        $this->setItem(13, $hazard);
        $this->setItem(16, $paper);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        switch($sourceItem->getId()) {

            case Item::NAMETAG:
                ServerManager::setSetting(ServerManager::ITEMSHOP, !ServerManager::isSettingEnabled(ServerManager::ITEMSHOP));
                $this->setItems();
                ServerManager::notify(ServerManager::ITEMSHOP);
                break;

            case Item::NETHER_STAR:
                ServerManager::setSetting(ServerManager::HAZARD, !ServerManager::isSettingEnabled(ServerManager::HAZARD));
                $this->setItems();
                ServerManager::notify(ServerManager::HAZARD);
                break;

            case Item::PAPER:
                ServerManager::setSetting(ServerManager::SHOP, !ServerManager::isSettingEnabled(ServerManager::SHOP));
                $this->setItems();
                ServerManager::notify(ServerManager::SHOP);
                break;
        }
        PacketManager::unClickButton($player);
        return true;
    }
}
