<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class BanCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("ban", "Komenda ban", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender)) return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /ban §8(§4nick§8) (§4powod§8)"));
			return;
		}

		$api = Main::getInstance()->getBanAPI();

		$player = Server::getInstance()->getPlayer($args[0]);

		$nick = $player == null ? $args[0] : $player->getName();

		if($api->isBanned($nick)) {
			$sender->sendMessage(Main::format("Ten gracz zostal juz zbanowany!"));
			return;
		}

		array_shift($args);

		$reason = isset($args[0]) ? trim(implode(" ", $args)) : "BRAK";

		$api->setBan($nick, $reason, $sender->getName());

		if($player != null)
		 $player->kick($api->getBanMessage($player), false);

		$sender->sendMessage(Main::format("Pomyslnke zbanowano gracza §4$nick §7z powodem: §4$reason"));

		Server::getInstance()->broadcastMessage(Main::formatLines(["Gracz §4$nick §7zostal zbanowany!", "Powod bana: §4$reason"]));
	}
}
