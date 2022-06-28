<?php

namespace Core\inventory;

use pocketmine\inventory\ContainerInventory;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use Core\tile\Beacon;

class BeaconInventory extends ContainerInventory {
	
	public function __construct(Beacon $tile){
		parent::__construct($tile);
	}
	
	public function getNetworkType() : int {
		return WindowTypes::BEACON;
	}
	
	public function getName() : String {
		return "Beacon";
	}
	
	public function getDefaultSize() : int {
		return 1;
	}
}