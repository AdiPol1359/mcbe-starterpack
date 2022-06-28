<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class HelpCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("help", "Komenda pomoc");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

	    $sender->getServer()->dispatchCommand($sender, "pomoc");
	}
}