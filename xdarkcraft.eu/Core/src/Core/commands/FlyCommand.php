<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class FlyCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("fly", "Komenda fly", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;
		 $nick = $sender->getName();
   if(empty($args)){
   	if(!isset($this->fly[$nick])){
   		$this->fly[$nick] = true;
   		$sender->setAllowFlight(true);
   		$sender->sendMessage("§8§l>§r §7Latanie zostalo §4wlaczone§7!");
   	}
   	else{
   		unset($this->fly[$nick]);
   		$sender->setAllowFlight(false);
   		$sender->sendMessage("§8§l>§r §7Latanie zostalo §cwylaczone§7!");
   	}
   	}
   }
}