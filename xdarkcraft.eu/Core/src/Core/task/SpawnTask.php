<?php

namespace Core\task;

use pocketmine\scheduler\Task;

use pocketmine\Player;

use pocketmine\Server;

use Core\Main;

class SpawnTask extends Task {
	
	private $player;
	
	public function __construct(Player $player) {
		$this->player = $player;
	}
	
	public function onRun(int $currentTick) {
		
		$player = $this->player;
		
		unset(Main::$spawnTask[$player->getName()]);
		
		if(Server::getInstance()->getPlayerExact($player->getName())) {
			
			$player->teleport($player->getLevel()->getSafeSpawn());
			
			$player->sendMessage(Main::format("Teleportacja zostala zakonczona pomyslnie"));
		}
	}
}