<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class AlertCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("alert", "Komenda alert", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender)) return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /alert §8(§4wiadomosc§8)"));
			return;
		}

		$message = trim(implode(" ", $args));

		foreach($sender->getServer()->getDefaultLevel()->getPlayers() as $player) {
			$player->addTitle("§l§4ALLERT", "§7{$message}");
		}
	}
}