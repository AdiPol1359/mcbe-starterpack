<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

use Core\form\WarpsForm;

class WarpCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("warp", "Komenda warp");
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}
		
		if(empty($args)) {
		 $sender->sendForm(new WarpsForm($sender));
		 return;
		}
		
		if(!$sender->hasPermission("PolishHard.warp.command")) {
			$sender->sendMessage(Main::format("Nie mozesz tego zrobic!"));
			return;
		}
		
		if(!isset($args[1])) {
			$sender->sendMessage(Main::formatLines([
			 "Uzyj:",
			 "/warp set (nazwa)",
			 "/warp remove (nazwa)"
			]));
			return;
		}
		
		$api = Main::getInstance()->getWarpsAPI();
		
		switch($args[0]) {
			case "set":
			 if($api->isWarpExists($args[1])) {
			 	$sender->sendMessage(Main::format("Ten warp juz istnieje!"));
			 	return;
			 }
			 
			 $api->setWarp($args[1], $sender);
			 $sender->sendMessage(Main::format("Pomyslnie ustawiono warpa!"));
			break;
			
			case "remove":
			 if(!$api->isWarpExists($args[1])) {
			 	$sender->sendMessage(Main::format("Ten warp nie istnieje!"));
			 	return;
			 }
			 
			 $api->removeWarp($args[1]);
			 $sender->sendMessage(Main::format("Pomyslnie usunieto warpa!"));
			break;
			
			default:
			 $sender->sendMessage(Main::format("Nieznany argument!"));
		}
	}
}