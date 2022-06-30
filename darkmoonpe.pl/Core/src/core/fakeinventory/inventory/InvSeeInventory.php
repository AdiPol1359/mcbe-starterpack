<?php

namespace core\fakeinventory\inventory;

use core\fakeinventory\FakeInventory;
use core\manager\managers\PacketManager;
use core\manager\managers\SoundManager;
use core\util\utils\InventoryUtil;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;

class InvSeeInventory extends FakeInventory {

    public string $targetPlayer;
    private array $ironBatsSlots = [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17];

    public function __construct(Player $player, string $targetPlayer) {

        ($targetCommand = Server::getInstance()->getPlayer($targetPlayer)) ? $targetName = $targetCommand->getName() : $targetName = $targetPlayer;
        $targetShortName = strlen($targetName) > 10 ? substr($targetName ,0,10) . "..." : $targetName;
        parent::__construct($player, "§8Ekwipunek §l§9".$targetShortName."§r§7!", self::BIG);

        $this->targetPlayer = $targetName;
        $this->setItems();
    }

    public function setItems() : void{

        $this->clearAll(true);

        for($x = 1; $x <= 9; $x++)
            $this->setItemAt($x, 2, Item::get(Item::IRON_BARS)->setCustomName(" "));

        for($x = 5; $x <= 9; $x++)
            $this->setItemAt($x, 1, Item::get(Item::IRON_BARS)->setCustomName(" "));

        ($targetPlayer = Server::getInstance()->getPlayer($this->targetPlayer)) ? $items = $targetPlayer->getInventory()->getContents() : $items = InventoryUtil::getOfflinePlayerItems($this->targetPlayer);
        $targetPlayer ? $armorInventoryItems = $targetPlayer->getArmorInventory()->getContents() : $armorInventoryItems = [];

        foreach($items as $slot => $item) {

            if($slot >= 100 && empty($armorInventoryItems)){
                $this->setItem(($slot - 100), $item);
                continue;
            }

            $this->setItem(($slot + ($targetPlayer ? 18 : 9)), $item);
        }

        if(!empty($armorInventoryItems)) {
            foreach($armorInventoryItems as $armorSlot => $armorItem)
                $this->setItem($armorSlot, $armorItem);
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        ($targetPlayer = Server::getInstance()->getPlayer($this->targetPlayer)) ? $items = $targetPlayer->getInventory()->getContents() : $items = InventoryUtil::getOfflinePlayerItems($this->targetPlayer);
        $targetPlayer ? $armorInventoryItems = $targetPlayer->getArmorInventory()->getContents() : $armorInventoryItems = [];

        $slots = ($targetPlayer ? 18 : 9);

        if(in_array($slot, $this->ironBatsSlots)){
            PacketManager::unClickButton($player);
            return true;
        }

        if(!$targetPlayer){

            if($slot <= 4){
                if($sourceItem->getId() !== Item::AIR)
                    InventoryUtil::removeItemFromSlot($this->targetPlayer, ($slot + 100));

                InventoryUtil::addItemToSlot($this->targetPlayer, $targetItem, ($slot + 100));

                return false;
            }

            if($targetItem->getId() !== Item::AIR) {

                if($sourceItem->getId() !== Item::AIR)
                    InventoryUtil::removeItemFromSlot($this->targetPlayer, $slot - $slots);

                InventoryUtil::addItemToSlot($this->targetPlayer, $targetItem, ($slot - $slots));

                return false;
            }

            $item = InventoryUtil::findItemBySlot($this->targetPlayer, $slot - $slots);

            if(!$item)
                return true;

            if($sourceItem->equals($item)){
                InventoryUtil::removeItemFromSlot($this->targetPlayer, $slot - $slots);

                return false;
            }
        }else{

            if($slot <= 4){
                if($sourceItem->getId() !== Item::AIR)
                    $targetPlayer->getArmorInventory()->setItem($slot, Item::get(Item::AIR));

                $targetPlayer->getArmorInventory()->setItem($slot, $targetItem);

                return false;
            }

            if($targetItem->getId() !== Item::AIR) {
                if($sourceItem->getId() !== Item::AIR)
                    $targetPlayer->getInventory()->setItem($slot - $slots, Item::get(Item::AIR));

                $targetPlayer->getInventory()->setItem($slot - $slots, $targetItem);

                return false;
            }

            $item = $targetPlayer->getInventory()->getItem(($slot - $slots));

            if(!$item)
                return true;

            if($sourceItem->equals($item)){
                $targetPlayer->getInventory()->setItem($slot - $slots, Item::get(Item::AIR));

                return false;
            }
        }

        return true;
    }
}