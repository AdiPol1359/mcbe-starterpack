<?php

namespace Core\task;

use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\math\Vector3;

use Core\Main;

class HomeTask extends Task {
	
	private $player;
	private $pos;
	private $homeName;

	public function __construct(Player $player, Vector3 $pos, string $homeName) {
		$this->player = $player;
		$this->pos = $pos;
		$this->homeName = $homeName;
	}
	
	public function onRun(int $currentTick) {
		$player = $this->player;
		
		unset(Main::$homeTask[$player->getName()]);
		
		if(Server::getInstance()->getPlayerExact($player->getName())) {
			$player->teleport($this->pos);
			$player->sendMessage(Main::format("Pomyslnie przeteleportowano do domu §4{$this->homeName}"));
		}
	}
}