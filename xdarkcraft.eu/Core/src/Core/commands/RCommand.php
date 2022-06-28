<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};
use pocketmine\level\sound\ClickSound;
use Core\Main;

class RCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("r", "Komenda r");
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}
		
		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /r §8(§4wiadomosc§8)"));
			return;
		}
		
		$msg = implode(" ", $args);
		
		if(!isset(Main::$msgR[$sender->getName()]) || !($player = $sender->getServer()->getPlayerExact(Main::$msgR[$sender->getName()]))) {
			$sender->sendMessage("§4Ja §8§l>§r §4{$sender->getName()}§8: §7$msg");
		$sender->sendMessage("§4{$sender->getName()} §8§l>§r §4Ja§8: §7$msg");
			return;
		}
		
		$sender->sendMessage("§4Ja §8§l>§r §4{$player->getName()}§8: §7$msg");
		$player->sendMessage("§4{$sender->getName()} §8§l>§r §4Ja§8: §7$msg");
        $player->getLevel()->addSound(new ClickSound($player), [$player]);
	}
}