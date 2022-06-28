<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class ClearlagCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("clearlag", "Komenda clearlag", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender)) return;

		Main::getInstance()->clearLag();
	}
}
