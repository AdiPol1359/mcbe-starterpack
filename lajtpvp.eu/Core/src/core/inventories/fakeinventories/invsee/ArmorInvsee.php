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

class ArmorInvsee extends FakeInventory {

    public string $targetPlayer;
    private array $armorSlots = [13, 22, 31, 40];

    public function __construct(string $targetPlayer) {
        ($targetCommand = Server::getInstance()->getPlayerByPrefix($targetPlayer)) ? $targetName = $targetCommand->getName() : $targetName = $targetPlayer;
        $targetShortName = strlen($targetName) > 10 ? substr($targetName ,0,10) . "..." : $targetName;

        $this->targetPlayer = $targetName;

        parent::__construct("§8Ekwipunek §l§e".$targetShortName."§r§7!", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void{
        $this->clearAll();

        $itemFactory = ItemFactory::getInstance();
        $ironBars = [12, 21, 30, 39, 14, 23, 32, 41];

        foreach($ironBars as $slot)
            $this->setItem($slot, $itemFactory->get(ItemIds::STAINED_GLASS_PANE, 7)->setCustomName(" "));
        
        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_UP_AND_DOWN_WITH_ROWS);

        ($targetPlayer = Server::getInstance()->getPlayerByPrefix($this->targetPlayer)) ? $items = $targetPlayer->getArmorInventory()->getContents() : $items = InventoryUtil::getOfflinePlayerArmorItems($this->targetPlayer);

        foreach($items as $slot => $item) {
            $this->setItem(13 + (9 * $slot), $item);
        }

        $this->setItem(49, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§l§cPOWROT"), true, true);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== ItemIds::STAINED_GLASS_PANE)
            SoundUtil::addSound([$player], $this->holder, "random.click");

        if($slot === 49) {
            $this->changeInventory($player, new ChoosePlayerInventory($this->targetPlayer));
            $this->unClickItem($player);
            return true;
        }

        ($targetPlayer = Server::getInstance()->getPlayerByPrefix($this->targetPlayer)) ? $items = $targetPlayer->getArmorInventory()->getContents() : $items = InventoryUtil::getOfflinePlayerItems($this->targetPlayer);

        $slots = (($slot - 13) / 9) + ($targetPlayer ? 0 : 100);

        if(!in_array($slot, $this->armorSlots)){
            $this->unClickItem($player);
            return true;
        }

        if(!$targetPlayer){
            if($targetItem->getId() !== ItemIds::AIR) {

                if($sourceItem->getId() !== ItemIds::AIR)
                    InventoryUtil::removeItemFromSlot($this->targetPlayer, $slots);

                InventoryUtil::addItemToSlot($this->targetPlayer, $targetItem, $slots);

                return false;
            }

            $item = InventoryUtil::findItemBySlot($this->targetPlayer, $slots);

            if(!$item)
                return true;

            if($sourceItem->equals($item, true, false)){
                InventoryUtil::removeItemFromSlot($this->targetPlayer, $slots);

                return false;
            }
        }else{
            if($targetItem->getId() !== ItemIds::AIR) {
                if($sourceItem->getId() !== ItemIds::AIR)
                    $targetPlayer->getArmorInventory()->setItem($slots, ItemFactory::air());

                $targetPlayer->getArmorInventory()->setItem($slots, $targetItem);

                return false;
            }

            $item = $targetPlayer->getArmorInventory()->getItem($slots);

            if($sourceItem->equals($item, true, false)){
                $targetPlayer->getArmorInventory()->setItem($slots, ItemFactory::air());
                return false;
            }
        }

        return true;
    }
}