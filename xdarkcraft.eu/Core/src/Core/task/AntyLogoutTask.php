<?php

namespace Core\task;

use pocketmine\scheduler\Task;

use pocketmine\Server;

use Core\Main;

class AntyLogoutTask extends Task {

	public function onRun(int $currentTick) {
		foreach(Main::$antylogoutPlayers as $nick => $time){
			$player = Server::getInstance()->getPlayerExact($nick);

			if(time() - $time >= Main::ANTYLOGOUT_TIME){
				
				unset(Main::$antylogoutPlayers[$nick]);
				Main::$assists[$nick] = [];
				$player->sendTip("§l§4AntyLogout");
				
				return;
			}

			$player->sendTip("§7AntyLogout: §c".(Main::ANTYLOGOUT_TIME - (time() - $time)));
		}
	}
}