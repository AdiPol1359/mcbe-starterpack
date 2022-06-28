<?php

namespace Core\task;

use pocketmine\Server;
use pocketmine\scheduler\Task;

class AlwaysDayTask extends Task {

	public function onRun(int $currentTick) : void {
		foreach(Server::getInstance()->getLevels() as $level) {
			if($level->getName() == "lobby")
		  $level->setTime(14000);
		 else
		  $level->setTime(100);
		}
	}
}