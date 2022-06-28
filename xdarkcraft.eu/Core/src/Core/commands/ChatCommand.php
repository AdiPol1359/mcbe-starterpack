<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class ChatCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("chat", "Komenda chat", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender)) return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /chat §8(§4on §7| §4off §7| §4cc§8)"));
			return;
		}

		switch($args[0]) {
			case "on":
			 Main::$chatOn = true;

		 	Server::getInstance()->broadcastMessage(Main::format("Chat zostal §4wlaczony §7przez §6".$sender->getName()."§7!"));
			break;

			case "off":
			 Main::$chatOn = false;

	 		Server::getInstance()->broadcastMessage(Main::format("Chat zostal §cwylaczony §7przez §6".$sender->getName()."§7!"));
			break;

			case "cc":
			    foreach($sender->getServer()->getDefaultLevel()->getPlayers() as $player) {
			        for($i = 0; $i <= 50; $i++)
			            $player->sendMessage(" ");

			        $player->sendMessage(Main::format("Chat zostal §ewyczyszczony §7przez §6{$sender->getName()}§7!"));
                }
			break;

			default:
			 $sender->sendMessage(Main::format("Poprawne uzycie: /chat §8(§4on §7| §4off §7| §4cc§8)"));
		}
	}
}
