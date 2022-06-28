<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class HelpopCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("helpop", "Komenda helpop");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /helpop §8(§4wiadomosc§8)"));
			return;
		}

		$message = trim(implode(" ", $args));

		foreach(Server::getInstance()->getOnlinePlayers() as $player) {
			if($player->hasPermission("PolishHard.helpop.message"))
			 $player->sendMessage("§l§4HELPOP§r §8§l>§r §f{$sender->getName()}§8: §7{$message}");
		}

		$sender->sendMessage(Main::format("Zgloszenie zostalo wyslane!"));
	}
}
