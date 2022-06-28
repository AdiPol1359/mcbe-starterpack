<?php

namespace Core\task;

use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\math\Vector3;

use Core\Main;

class WarpTask extends Task {
	
	private $player;
	private $pos;
	private $warpName;

	public function __construct(Player $player, Vector3 $pos, string $warpName) {
		$this->player = $player;
		$this->pos = $pos;
		$this->warpName = $warpName;
	}
	
	public function onRun(int $currentTick) {
		$player = $this->player;
		
		unset(Main::$warpTask[$player->getName()]);
		
		if(Server::getInstance()->getPlayerExact($player->getName())) {
			$player->teleport($this->pos);
			$player->sendMessage(Main::format("Pomyslnie przeteleportowano na warp §4{$this->warpName}"));
		}
	}
}