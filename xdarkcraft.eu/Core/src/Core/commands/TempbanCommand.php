<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class TempbanCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("tempban", "Komenda tempban", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
		    return;

		if(empty($args) || !isset($args[1])) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /tempban §8(§4nick§8) (§4czas§7[§4h§8/§4m§8/§4s§7]§8) (§4powod§8)"));
			return;
		}

		$api = Main::getInstance()->getBanAPI();

		$player = Server::getInstance()->getPlayer($args[0]);

		$nick = $player == null ? $args[0] : $player->getName();

		if($api->isBanned($nick)) {
			$sender->sendMessage(Main::format("Ten gracz zostal juz zbanowany!"));
			return;
		}

		if(!strpos($args[1], "d") && !strpos($args[1], "h") && !strpos($args[1], "m") && !strpos($args[1], "s")){
			 $sender->sendMessage(Main::format("Nieprawidlowy format czasu!"));
 			return;
  }

  $time = 0;

 	if(strpos($args[1], "d"))
		$time = intval(explode("d", $args[1])[0]) * 86400;

 	if(strpos($args[1], "h"))
		$time = intval(explode("h", $args[1])[0]) * 3600;

		if(strpos($args[1], "m"))
		$time = intval(explode("h", $args[1])[0]) * 60;

		if(strpos($args[1], "s"))
		$time = intval(explode("s", $args[1])[0]);

		$reason = "";

		for($i = 2; $i <= count($args) - 1; $i++)
		 $reason .= $args[$i] . " ";

		if($reason == "") $reason = "BRAK";

		$api->setTempBan($nick, $reason, $sender->getName(), $time);

		if($player != null)
		 $player->kick($api->getBanMessage($player), false);

		$sender->sendMessage(Main::format("Pomyslnke zbanowano gracza §4$nick §7na czas §4$args[1] §7z powodem: §4$reason"));
				Server::getInstance()->broadcastMessage(Main::formatLines(["Gracz §4$nick §7zostal zbanowany!", "Powod bana: §4$reason", "Dlugosc bana: §4$time"]));
		
		
	}
}