<?php

declare(strict_types=1);

namespace Gildie\fakeinventory;

use pocketmine\Player;
use Gildie\guild\GuildManager;
use pocketmine\item\Item;
use pocketmine\Server;

class GuildItemsInventory extends FakeInventory {
	
	public function __construct(Player $player) {
		parent::__construct($player, "§r§l§4ITEMY NA GILDIE");
		$this->setItems($player);
        $this->setCancelTransaction();
	}
	
	private function setItems(Player $player) : void {
		$nick = $player->getName();
		$slot = 10;
		foreach(GuildManager::getItems($player) as $item) {
		 $this->setItem($slot, $item);
		 
		 if($item->getId() == Item::ENCHANTED_GOLDEN_APPLE) {
		 	$db = Server::getInstance()->getPluginManager()->getPlugin("Core")->getDb();
		 	$array = $db->query("SELECT * FROM depozyt WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);
		 	
		 	$invCount = 0;
		 	
		 	foreach($player->getInventory()->getContents() as $invItem)
		 	 if($invItem->getId() == Item::ENCHANTED_GOLDEN_APPLE)
		 	  $invCount += $invItem->getCount();
		 	
		 	$depozytCount = $array["koxy"];
		 	
		 	if($invCount < $item->getCount() && ($invCount + $depozytCount) < $item->getCount()) {
		 	 $this->setItem($slot - 9, Item::get(Item::WOOL, 14));
		 	 $this->setItem($slot + 9, Item::get(Item::WOOL, 14));
		 	} else {
		 		$this->setItem($slot - 9, Item::get(Item::WOOL, 5));
		 	 $this->setItem($slot + 9, Item::get(Item::WOOL, 5));
		 	}
		 	$slot++;
		 	continue;
		 }
		 
		 if($item->getId() == Item::GOLDEN_APPLE) {
		 	$db = Server::getInstance()->getPluginManager()->getPlugin("Core")->getDb();
		 	$array = $db->query("SELECT * FROM depozyt WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);
		 	
		 	$invCount = 0;
		 	
		 	foreach($player->getInventory()->getContents() as $invItem)
		 	 if($invItem->getId() == Item::GOLDEN_APPLE)
		 	  $invCount += $invItem->getCount();
		 	
		 	$depozytCount = $array["refy"];
		 	
		 	if($invCount < $item->getCount() && ($invCount + $depozytCount) < $item->getCount()) {
		 	 $this->setItem($slot - 9, Item::get(Item::WOOL, 14));
		 	 $this->setItem($slot + 9, Item::get(Item::WOOL, 14));
		 	} else {
		 		$this->setItem($slot - 9, Item::get(Item::WOOL, 5));
		 	 $this->setItem($slot + 9, Item::get(Item::WOOL, 5));
		 	}
		 	$slot++;
		 	continue;
		 }
		 
		 $this->setItem($slot - 9, $player->getInventory()->contains($item) ? Item::get(Item::WOOL, 5) : Item::get(Item::WOOL, 14));
		 $this->setItem($slot + 9, $player->getInventory()->contains($item) ? Item::get(Item::WOOL, 5) : Item::get(Item::WOOL, 14));
		 $slot++;
		}
		
		$lvl = 100;
		
		if($player->hasPermission("nicecraft.gildie.sponsor"))
		 $lvl = 50;
		
		$exp = Item::get(Item::EXPERIENCE_BOTTLE);
		$exp->setCustomName("§r§4Level: ".$lvl);
		
		$this->setItem($slot, $exp);
		$this->setItem($slot - 9, $player->getXpLevel() >= $lvl ? Item::get(Item::WOOL, 5) : Item::get(Item::WOOL, 14));
		 $this->setItem($slot + 9, $player->getXpLevel() >= $lvl ? Item::get(Item::WOOL, 5) : Item::get(Item::WOOL, 14));
	}
}