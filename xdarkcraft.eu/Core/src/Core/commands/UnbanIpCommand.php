<?php

namespace Core\commands;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class UnbanIpCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("unban-ip", "Komenda unban-ip", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
		    return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /unban-ip §8(§4ip§8)"));
			return;
		}

		$api = Main::getInstance()->getBanAPI();

		if(!$api->isIpBanned($args[0])) {
			$sender->sendMessage(Main::format("To Ip nie jest zbanowane!"));
			return;
		}

		$api->unbanIp($args[0]);

		$sender->sendMessage(Main::format("Pomyslnie odbanowano IP §4$args[0]§7!"));
	}
}
