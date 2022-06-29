<?php

namespace core\task\tasks;

use core\fakeinventory\FakeInventory;
use pocketmine\scheduler\Task;
use pocketmine\Player;

class OpenFakeInventoryTask extends Task {
	
	private $player;
	private $inventory;
	
	public function __construct(Player $player, FakeInventory $inventory) {
		$this->player = $player;
		$this->inventory = $inventory;
	}
	
	public function onRun(int $currentTick) : void  {
		$this->inventory->openFor([$this->player]);
	}
}