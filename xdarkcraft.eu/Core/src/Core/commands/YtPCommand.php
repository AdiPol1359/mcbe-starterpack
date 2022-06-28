<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class YtPCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("yt+", "Komenda YT+");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		  $sender->sendMessage(" \n§8          §4§lPolishHard§7.EU\n§r");
				$sender->sendMessage(" §7Chcesz zdobyc range §l§4YT§r§7?");
				$sender->sendMessage("   §7Musisz posiadac §l§4500§r §7subow oraz §l§4trailer§r §7na kanale");
				$sender->sendMessage("   §7Twoje filmy musza posiadac srednio §l§4120§r §7wyswietlen");
				$sender->sendMessage(" §7Permisje Rangi §l§4YT§r§7:");
				$sender->sendMessage(" §8- §7/kit yt");
				$sender->sendMessage(" §8- §7/kit yt+");
				$sender->sendMessage(" §8- §7/repair");
				$sender->sendMessage(" §8- §7/ec");
                $sender->sendMessage(" §8- §4+40% §7do dropu");
				$sender->sendMessage(" §8- §7-25% Itemow na gildie");
				$sender->sendMessage(" §8- §7Krotszy czas teleportacji §8(§f5s§8)");
				$sender->sendMessage("   §7Mozesz trailer pobrac z naszej strony WWW: §l§4www.PolishHard.EU\n ");
	}
}