<?php

namespace Core\task;

use pocketmine\scheduler\Task;

use pocketmine\Server;

use pocketmine\utils\Config;

use Core\Main;

class BotTask extends Task {

	private $config;

	public function __construct(Config $config) {
		$this->config = $config;
	}

	public function onRun(int $currentTick) {

		$msg = $this->config->get("messages")[mt_rand(0, count($this->config->get("messages")) - 1)];
		
		//foreach(Server::getInstance()->getDefaultLevel()->getPlayers() as $p)
		 //$p->sendMessage("§f§lx§4§lDark§f§lCraft§4.EU §r§8§l>§r §7".$msg);
	}
}