<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};
use Core\Main;
use Core\api\HomeAPI;

class SethomeCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("sethome", "Komenda sethome");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

	    if(empty($args)) {
	        $sender->sendMessage(Main::format("Poprawne uzycie: /sethome §8(§4nazwa domu§8)"));
	        return;
        }

	    if(HomeAPI::getHomesCount($sender) >= HomeAPI::getMaxHomesCount($sender)) {
	        $sender->sendMessage(Main::format("Nie mozesz stworzyc wiecej domow! §8(§4".HomeAPI::getMaxHomesCount($sender)."§8)"));
	        return;
        }

	    if(HomeAPI::isHomeExists($sender, $args[0])) {
	        $sender->sendMessage(Main::format("Ten dom juz istnieje!"));
	        return;
        }

	    HomeAPI::setHome($sender, $args[0], $sender->asVector3());

	    $sender->sendMessage(Main::format("Utworzono dom o nazwie §4$args[0]"));
	}
}