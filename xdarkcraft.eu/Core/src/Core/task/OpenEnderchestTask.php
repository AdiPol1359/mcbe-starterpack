<?php

namespace Core\task;

use pocketmine\Player;

use pocketmine\scheduler\Task;

use pocketmine\math\Vector3;

use Core\inventory\EnderchestInventory;

class OpenEnderchestTask extends Task {
	
	private $callback;
	
	public function __construct(Player $player, ?Vector3 $pos, int $size) {
		$this->player = $player;
		$this->pos = $pos;
		$this->size = $size;
	}
	
	public function onRun(int $currentTick) : void {
		$this->player->addWindow(new EnderchestInventory($this->player, $this->pos, $this->size));
	}
}