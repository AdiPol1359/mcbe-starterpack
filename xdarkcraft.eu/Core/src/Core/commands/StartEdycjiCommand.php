<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class StartEdycjiCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("startedycji", "Komenda startedycji", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		if(empty($args)) {
			$sender->sendMessage(Main::formatLines(["Poprawne uzycie:", "/startedycji §8(§4on §7| §4off§8)", "/startedycji settime §8(§4H§7:§4M§8)"]));
			return;
		}
		
		switch($args[0]) {
			case "on":
			 Main::getInstance()->setStartEdycji(true);
			 $sender->sendMessage(Main::format("Pomyslnie wlaczono start edycji"));
			break;
			
			case "off":
			 Main::getInstance()->setStartEdycji(false);
			 $sender->sendMessage(Main::format("Pomyslnie wylaczono start edycji"));
			break;
			
			case "settime":
			
			 if(!isset($args[1])) {
			 	$sender->sendMessage(Main::format("Poprawne uzycie: /startedycji settime §8(§4H§7:§4M§8)"));
			 	return;
			 }
			 
			 $words = explode(':', $args[1]);
			 
			 if(!is_numeric($words[0]) || !is_numeric($words[1]) || $words[0] > 24 || $words[1] > 59) {
			 	$sender->sendMessage(Main::format("Nieprawidlowy format godziny!"));
			 	return;
			 }
			 
			 Main::getInstance()->setStartEdycjiTime($args[1]);
			 $sender->sendMessage(Main::format("Pomyslnie ustawiono godzine wylaczenia startu edycji na §4$args[1]"));
			break;
			
			default:
			 $sender->sendMessage(Main::formatLines(["Poprawne uzycie:", "/startedycji §8(§4on §7| §4off§8)", "/startedycji settime §8(§4H§7:§4M§8)"]));
		}
	}
}