<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class ListCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("list", "Komenda list");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

   $sender->sendMessage("§8§l>§r §7Aktualna liczba graczy online: §4".count(Server::getInstance()->getOnlinePlayers()));
	}
}