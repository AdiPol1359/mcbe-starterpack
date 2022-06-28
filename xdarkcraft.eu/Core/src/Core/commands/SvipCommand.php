<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class SvipCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("svip", "Komenda svip");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		  $sender->sendMessage(" \n§8          §4§lPolishHard§7.EU\n§r");
				$sender->sendMessage(" §7Chcesz zakupic range §l§4SVIP§r§7?");
				$sender->sendMessage(" §7Permisje rangi §l§4SVIP§r§7:");
				$sender->sendMessage(" §8- §7/kit vip");
				$sender->sendMessage(" §8- §7/kit svip");
                $sender->sendMessage(" §8- §7/kit tnt");
				$sender->sendMessage(" §8- §7/ec");
				$sender->sendMessage(" §8- §7/repair");
				$sender->sendMessage(" §8- §7/feed");
                $sender->sendMessage(" §8- §4+50% §7do dropu");
				$sender->sendMessage(" §8- §4-25% §7Itemow na gildie");
				$sender->sendMessage(" §8- §7Powiekszony enderchest");
                $sender->sendMessage(" §8- §7Przenosny enchanting");
                $sender->sendMessage(" §8- §7Mozliwosc ustawienia §45 §7home'ów");
				$sender->sendMessage(" §8- §7Krotszy czas teleportacji §8(§f5s§8)");
				$sender->sendMessage(" §7Jak aktywowac range §l§4SVIP§r§7?");
				$sender->sendMessage("   §7Skorzystaj z naszej strony WWW: §l§4www.PolishHard.EU\n ");
	}
}