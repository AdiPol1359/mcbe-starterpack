<?php

namespace Core\network\protocol\types;

use pocketmine\block\RedstoneTorch;
use pocketmine\inventory\BaseInventory;
use pocketmine\Player;

use pocketmine\inventory\CraftingGrid;

use pocketmine\network\mcpe\protocol\types\{
	NetworkInventoryAction as PMNetworkInventoryAction, ContainerIds, WindowTypes
};

use pocketmine\inventory\transaction\action\{
	CreativeInventoryAction, SlotChangeAction, DropItemAction
};
use pocketmine\inventory\ContainerInventory;
use Core\inventory\{
	VillagerInventory, BeaconInventory
};

class NetworkInventoryAction extends PMNetworkInventoryAction {

    public function createInventoryAction(Player $player){
        $player->getServer()->broadcastMessage("sourceType: " . $this->sourceType . " windowId: " . $this->windowId . " inventorySlot: " . $this->inventorySlot . " flags: " . $this->sourceFlags);
        switch($this->sourceType){
            case self::SOURCE_CONTAINER:
                $window = $player->findWindow(ContainerInventory::class);
                if($window !== null){
                    return new SlotChangeAction($window, $this->inventorySlot, $this->oldItem, $this->newItem);
                }

                throw new \UnexpectedValueException("Player " . $player->getName() . " has no open container with window ID $this->windowId");
            case self::SOURCE_WORLD:
                if($this->inventorySlot !== self::ACTION_MAGIC_SLOT_DROP_ITEM){
                    throw new \UnexpectedValueException("Only expecting drop-item world actions from the client!");
                }

                return new DropItemAction($this->newItem);
            case self::SOURCE_CREATIVE:
                switch($this->inventorySlot){
                    case self::ACTION_MAGIC_SLOT_CREATIVE_DELETE_ITEM:
                        $type = CreativeInventoryAction::TYPE_DELETE_ITEM;
                        break;
                    case self::ACTION_MAGIC_SLOT_CREATIVE_CREATE_ITEM:
                        $type = CreativeInventoryAction::TYPE_CREATE_ITEM;
                        break;
                    default:
                        throw new \UnexpectedValueException("Unexpected creative action type $this->inventorySlot");

                }

                return new CreativeInventoryAction($this->oldItem, $this->newItem, $type);
            case self::SOURCE_TODO:
                $window = $player->findWindow(ContainerInventory::class);

                switch($this->windowId){
                    case -100: //TODO: this type applies to all fake windows, not just crafting
                        return new SlotChangeAction($window ?? $player->getCraftingGrid(), $this->inventorySlot, $this->oldItem, $this->newItem);
                    case self::SOURCE_TYPE_CRAFTING_RESULT:
                    case self::SOURCE_TYPE_CRAFTING_USE_INGREDIENT:
                        return null;
                    case -10:
                    case -11:
                        return new SlotChangeAction($window, $this->inventorySlot, $this->oldItem, $this->newItem);
                    case -12:
                        return new SlotChangeAction($window, $this->inventorySlot, $this->oldItem, $this->newItem);
                }

                throw new \UnexpectedValueException("Player " . $player->getName() . " has no open container with window ID $this->windowId");
            default:
                throw new \UnexpectedValueException("Unknown inventory source type $this->sourceType");
        }
    }
}