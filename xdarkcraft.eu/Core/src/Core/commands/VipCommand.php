<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class VipCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("vip", "Komenda vip");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		  $sender->sendMessage(" \n§8          §4§lPolishHard§7.EU\n§r");
				$sender->sendMessage(" §7Chcesz zakupic range §l§4VIP§r§7?");
				$sender->sendMessage("   §7Koszt to §l§44§r§7.§l§492§r§7zl");
				$sender->sendMessage(" §7Permisje rangi §l§4VIP§r§7:");
				$sender->sendMessage(" §8- §7/kit vip");
				$sender->sendMessage(" §8- §7/ec");
				$sender->sendMessage(" §8- §7/repair");
                $sender->sendMessage(" §8- §4+40% §7do dropu");
				$sender->sendMessage(" §8- §4-15% §7Itemow na gildie");
                $sender->sendMessage(" §8- §7Mozliwosc ustawienia §43 §7home'ów");
				$sender->sendMessage(" §8- §7Krotszy czas teleportacji §8(§f7s§8)");
				$sender->sendMessage(" §7Jak aktywowac range §l§4VIP§r§7?");
				$sender->sendMessage("   §7Skorzystaj z naszej strony WWW: §l§4wwww.PolishHard.EU\n ");
	}
}