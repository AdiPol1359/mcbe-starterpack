<?php

namespace Core\commands;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class UnbanCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("unban", "Komenda unban", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
		    return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /unban §8(§4nick§8)"));
			return;
		}

		$api = Main::getInstance()->getBanAPI();

		if(!$api->isBanned($args[0])) {
			$sender->sendMessage(Main::format("Ten gracz nie jest zbanowany!"));
			return;
		}

		$api->unban($args[0]);

		$sender->sendMessage(Main::format("Pomyslnie odbanowano gracza §4$args[0]§7!"));
	}
}
