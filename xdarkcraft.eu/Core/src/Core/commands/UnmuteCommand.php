<?php

namespace Core\commands;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class UnmuteCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("unmute", "Komenda unmute", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
		    return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /unmute §8(§4nick§8)"));
			return;
		}

		$api = Main::getInstance()->getMuteAPI();
		
		$player = $sender->getServer()->getPlayer($args[0]);
		
		$nick = $player == null ? $args[0] : $player->getName();

		if(!$api->isMuted($nick)) {
			$sender->sendMessage("§8§l>§r §7Ten gracz nie jest zmutowany!");
			return;
		}

		$api->unmute($nick);

		$sender->sendMessage(Main::format("Pomyslnie odmutowano gracza §4{$nick}§7!"));
		
		if($player !== null)
		 $player->sendMessage(Main::format("Zostales odmutowany!"));
	}
}