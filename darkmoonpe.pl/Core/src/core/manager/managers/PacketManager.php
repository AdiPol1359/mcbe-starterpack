<?php

namespace core\manager\managers;

use core\manager\BaseManager;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\Player;

class PacketManager extends BaseManager {

    public static function unClickButton(Player $player) : void {
        $packet = new InventorySlotPacket();
        $packet->windowId = ContainerIds::UI;
        $packet->inventorySlot = 0;
        $packet->item = ItemStackWrapper::legacy(Item::get(Item::AIR));
        $player->sendDataPacket($packet);
    }
}