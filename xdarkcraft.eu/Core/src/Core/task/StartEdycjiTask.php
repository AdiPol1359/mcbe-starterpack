<?php

namespace Core\task;

use pocketmine\scheduler\Task;

use Core\Main;

class StartEdycjiTask extends Task {

	public function onRun($tick){
		if(Main::getInstance()->startEdycji())
			if(time() >= strtotime(Main::getInstance()->getStartEdycjiTime()))
				Main::getInstance()->setStartEdycji(false);
	}
}