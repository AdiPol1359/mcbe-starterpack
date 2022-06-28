<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class AchatCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("achat", "Komenda achat", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender)) return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /achat §8(§4wiadomosc§8)"));
			return;
		}

		$message = trim(implode(" ", $args));

		foreach(Server::getInstance()->getOnlinePlayers() as $player) {
			if($player->hasPermission("PolishHard.achat.message"))
			 $player->sendMessage("§l§4ADMINCHAT§r §8§l>§r §c{$sender->getName()}§8: §6{$message}");
		}
	}
}
