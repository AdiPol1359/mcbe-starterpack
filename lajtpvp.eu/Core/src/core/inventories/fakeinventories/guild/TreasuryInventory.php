<?php

namespace core\inventories\fakeinventories\guild;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryManager;
use core\guilds\Guild;
use core\inventories\FakeInventorySize;
use core\utils\Settings;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class TreasuryInventory extends FakeInventory {

    private ?Guild $guild;

    public function __construct(private Player $player, Guild $guild) {
        $this->guild = $guild;

        parent::__construct("§l§eSKARBIEC", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void {
        if(!$this->guild)
            return;

        if(!$this->guild->existsPlayer($this->player->getName())) {
            if(!$this->player->hasPermission(Settings::$PERMISSION_TAG."command.guildadmin"))
                return;
        }

        $this->fill(ItemIds::AIR);
        foreach($this->guild->getTreasury() as $slot => $item)
            $this->setItem($slot, $item ?? ItemFactory::air());
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if(!$this->guild) {
            $this->closeFor($player);
            return true;
        }

        if(!$this->guild->existsPlayer($this->player->getName())) {
            if(!$this->player->hasPermission(Settings::$PERMISSION_TAG . "command.guildadmin")) {
                $this->closeFor($player);
                return true;
            }
        }

        if(($clickedItem = $this->guild->getItemFromTreasury($slot))) {
            if($sourceItem->getId() !== $targetItem->getId()) {
                if(!$clickedItem->equals($sourceItem))
                    return true;

                $this->guild->removeItemFromTreasury($slot);

                foreach(FakeInventoryManager::getInventories() as $playerInv => $inventory) {
                    if($inventory instanceof TreasuryInventory)
                        $inventory->setItems();
                }
            }

            $this->guild->addItemToTreasury($slot, $targetItem);
        }

        foreach(FakeInventoryManager::getInventories() as $playerInv => $inventory) {
            if($inventory instanceof TreasuryInventory)
                $inventory->setItems();
        }

        return false;
    }
}