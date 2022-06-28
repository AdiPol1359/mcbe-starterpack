<?php

declare(strict_types=1);

namespace permissionex\task;

use pocketmine\scheduler\Task;
use permissionex\Main;

class GroupsTask extends Task {
	
	public function onRun(int $currentTick) {
		Main::getInstance()->getProvider()->taskProccess();
	}
}