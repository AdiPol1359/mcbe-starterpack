<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class PktCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("pkt", "Komenda pkt", false, ["points", "punkty", "mojepunkty"]);
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}
		
		 if(empty($args))
	 	 $sender->sendMessage(Main::format("Twoje punkty: §4".Main::getInstance()->getPointsAPI()->getPoints($sender->getName())));
	 	
	 	if(isset($args[0])) {
	 		if(Main::getInstance()->getPointsAPI()->getPoints($args[0]) == null) {
	 			$sender->sendMessage(Main::format("Nie znaleziono gracza w bazie!"));
	 			return;
	 		}
	 		
	 		$sender->sendMessage(Main::format("Punkty gracza {$args[0]}: §4".Main::getInstance()->getPointsAPI()->getPoints($args[0])));
	 }
	}
}