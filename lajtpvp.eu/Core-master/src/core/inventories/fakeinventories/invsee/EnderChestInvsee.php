<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\invsee;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryPatterns;
use core\inventories\FakeInventorySize;
use core\utils\InventoryUtil;
use core\utils\SoundUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\Server;

class EnderChestInvsee extends FakeInventory {

    public string $targetPlayer;
    private array $ironBatsSlots = [0, 1, 2, 3, 4, 5, 6, 7, 8, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53];

    public function __construct(string $targetPlayer) {
        ($targetCommand = Server::getInstance()->getPlayerByPrefix($targetPlayer)) ? $targetName = $targetCommand->getName() : $targetName = $targetPlayer;
        $targetShortName = strlen($targetName) > 10 ? substr($targetName ,0,10) . "..." : $targetName;

        $this->targetPlayer = $targetName;

        parent::__construct("§8Ekwipunek §l§e".$targetShortName."§r§7!", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void{
        $this->clearAll();
        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_UP_AND_DOWN);

        $itemFactory = ItemFactory::getInstance();

        foreach(range(36, 44) as $slot)
            $this->setItem($slot, $itemFactory->get(ItemIds::INVISIBLE_BEDROCK)->setCustomName(" "));

        ($targetPlayer = Server::getInstance()->getPlayerByPrefix($this->targetPlayer)) ? $items = $targetPlayer->getEnderInventory()->getContents() : $items = InventoryUtil::getOfflinePlayerEnderChestItems($this->targetPlayer);

        foreach($items as $slot => $item) {
            if($slot >= 100){
                $this->setItem(($slot - 100), $item);
                continue;
            }

            $this->setItem(($slot + ($targetPlayer ? 9 : 0)), $item);
        }

        $this->setItem(49, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§l§cPOWROT"), true, true);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== ItemIds::STAINED_GLASS_PANE && !$sourceItem->getId() !== ItemIds::INVISIBLE_BEDROCK)
            SoundUtil::addSound([$player], $this->holder, "random.click");

        if($slot === 49) {
            $this->changeInventory($player, new ChoosePlayerInventory($this->targetPlayer));
            $this->unClickItem($player);
            return true;
        }

        ($targetPlayer = Server::getInstance()->getPlayerByPrefix($this->targetPlayer)) ? $items = $targetPlayer->getEnderInventory()->getContents() : $items = InventoryUtil::getOfflinePlayerEnderChestItems($this->targetPlayer);

        $slots = ($targetPlayer ? 9 : 0);

        if(in_array($slot, $this->ironBatsSlots)){
            $this->unClickItem($player);
            return true;
        }

        if(!$targetPlayer){
            if($targetItem->getId() !== ItemIds::AIR) {

                if($sourceItem->getId() !== ItemIds::AIR)
                    InventoryUtil::removeItemFromSlot($this->targetPlayer, $slot - $slots);

                InventoryUtil::addItemToSlot($this->targetPlayer, $targetItem, ($slot - $slots));

                return false;
            }

            $item = InventoryUtil::findItemBySlot($this->targetPlayer, $slot - $slots);

            if(!$item)
                return true;

            if($sourceItem->equals($item, true, false)){
                InventoryUtil::removeItemFromSlot($this->targetPlayer, $slot - $slots);

                return false;
            }
        }else{
            if($targetItem->getId() !== ItemIds::AIR) {
                if($sourceItem->getId() !== ItemIds::AIR)
                    $targetPlayer->getEnderInventory()->setItem($slot - $slots, ItemFactory::air());

                $targetPlayer->getEnderInventory()->setItem($slot - $slots, $targetItem);

                return false;
            }

            $item = $targetPlayer->getEnderInventory()->getItem(($slot - $slots));

            if($sourceItem->equals($item, true, false)){
                $targetPlayer->getEnderInventory()->setItem($slot - $slots, ItemFactory::air());
                return false;
            }
        }

        return true;
    }
}