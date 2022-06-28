<?php

namespace Core\commands;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class PrzyznajesieCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("przyznajesie", "Komenda przyznajesie");
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		$nick = $sender->getName();
		
		if(!isset(Main::$spr[$nick])) {
			$sender->sendMessage(Main::format("Musisz byc sprawdzany aby uzyc tej komendy!"));
			return;
		}
		
		$api = Main::getInstance()->getBanAPI();
		
		$api->setTempBan($nick, "Przyznanie sie do cheatow", Main::$spr[$nick][1], (24 * 3600) * 3);
		
		$sender->teleport($sender->getLevel()->getSafeSpawn());
		
		unset(Main::$spr[$nick]);
		
		$sender->kick($api->getBanMessage($sender), false);
		
		$sender->getServer()->broadcastMessage(Main::format("Gracz §4$nick §7przyznal sie do cheatow"));
	}
}