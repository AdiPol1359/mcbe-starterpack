<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};
use Core\Main;
use Core\api\HomeAPI;

class DelhomeCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("delhome", "Komenda delhome");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

	    if(empty($args)) {
	        $sender->sendMessage(Main::format("Poprawne uzycie: /delhome §8(§4nazwa domu§8)"));
	        return;
        }

	    if(!HomeAPI::isHomeExists($sender, $args[0])) {
	        $sender->sendMessage(Main::format("Ten dom nie istnieje!"));
	        return;
        }

	    HomeAPI::deleteHome($sender, $args[0]);

	    $sender->sendMessage(Main::format("Pomyslnie usunieto dom §4$args[0]"));
	}
}