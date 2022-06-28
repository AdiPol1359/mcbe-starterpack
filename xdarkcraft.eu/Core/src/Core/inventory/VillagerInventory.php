<?php

namespace Core\inventory;

use pocketmine\inventory\BaseInventory;

use pocketmine\Player;

use pocketmine\item\Item;

use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

use pocketmine\nbt\tag\{CompoundTag, IntTag, ByteTag, ListTag};

use Core\entity\Villager;

class VillagerInventory extends BaseInventory {
	
	protected $holder;
	
	public const NETWORK_ID = 3;
	
	public function __construct(Villager $holder){
		$this->holder = $holder;
		parent::__construct();
	}
	
	public function getName() : String {
		return $this->holder->getCustomName();
	}
	
	public function getNetworkType() : int{
		return WindowTypes::TRADING;
	}
	
	public function getDefaultSize() : int {
		return 3;
	}
	
	public function onOpen(Player $who) : void {
		
		parent::onOpen($who);

		$pk = new UpdateTradePacket();
  $pk->windowId = self::NETWORK_ID;
  $pk->tradeTier = 1;
  $pk->traderEid = $this->getHolder()->getId();
  $pk->playerEid = $who->getId();
  $pk->displayName = $this->getName();
  $pk->isWilling = false;
  $pk->isV2Trading = false;
  $pk->offers = $this->holder->getOffers();
  $who->dataPacket($pk);
  
	}
	
	public function getHolder() : Villager {
		return $this->holder; 
	}
}