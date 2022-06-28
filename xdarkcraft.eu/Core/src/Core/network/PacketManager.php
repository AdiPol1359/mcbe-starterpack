<?php

namespace Core\network;

use pocketmine\network\mcpe\protocol\PacketPool;

use Core\network\protocol\{InventoryTransactionPacket};

class PacketManager {
	
	public static function init() : void {
		//PacketPool::registerPacket(new InventoryTransactionPacket());
	}
}