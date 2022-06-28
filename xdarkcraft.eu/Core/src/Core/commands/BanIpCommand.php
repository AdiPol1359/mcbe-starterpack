<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class BanIpCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("ban-ip", "Komenda ban-ip", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender)) return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /ban-ip §8(§4nick§8) (§4powod§8)"));
			return;
		}

		$api = Main::getInstance()->getBanAPI();

		$player = Server::getInstance()->getPlayer($args[0]);

		if($player == null) {
			$sender->sendMessage(Main::format("Ten gracz jest §4offline"));
			return;
		}

		$ip = $player->getAddress();

		if($api->isIpBanned($ip)) {
			$sender->sendMessage(Main::format("To IP zostalo juz zbanowane!"));
			return;
		}

		array_shift($args);

		$reason = isset($args[0]) ? trim(implode(" ", $args)) : "BRAK";

		$api->setIpBan($reason, $sender->getName(), $ip);

		foreach(Server::getInstance()->getOnlinePlayers() as $p) {
			if($p->getAddress() == $ip)
			 $p->kick($api->getBanMessage($p), false);
		}

		$sender->sendMessage(Main::format("Pomyslnke zbanowano gracza §4{$player->getName()} §7na IP z powodem: §4$reason"));
	}
}