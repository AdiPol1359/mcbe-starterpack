<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class TpdenyCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("tpdeny", "Komenda tpdeny");
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
		    return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}

	$nick = $sender->getName();

	if(empty($args)) {
	    if(empty(Main::$tp[$nick])) {
	        $sender->sendMessage(Main::format("Nikt nie wyslal do ciebie prosby o teleportacje!"));
	        return;
	    }

	    if(count(Main::$tp[$nick]) == 1) {
	        $player = $sender->getServer()->getPlayer(key(Main::$tp[$nick]));

	        unset(Main::$tp[$nick][$player->getName()]);
	        $player->sendMessage(Main::format("Gracz §4$nick §7odrzucil twoja prosbe o teleportacje"));
	    } else {
	        $sender->sendMessage(Main::format("Twoje prosby o teleportacje: "));

	        $requests = [];

	        foreach(Main::$tp[$nick] as $p => $time)
	            $requests[] = $p;

	        $sender->sendMessage(Main::format(implode(", ", $requests)));
	    }
	    return;
	}

	if($args[0] == "*") {
	    foreach(Main::$tp[$nick] as $player => $time) {
	        $player = $sender->getServer()->getPlayer($player);

	        unset(Main::$tp[$nick][$player->getName()]);
	        $player->sendMessage(Main::format("Gracz §4$nick §7odrzucil twoja prosbe o teleportacje"));
	    }
	} else {
	    $player = $sender->getServer()->getPlayer($args[0]);

	    if($player == null || !isset(Main::$tp[$nick][$player->getName()])) {
	        $sender->sendMessage(Main::format("Ten gracz nie wyslal do ciebie porsby o teleportacje"));
	        return;
	    }

	   unset(Main::$tp[$nick][$player->getName()]);
	        $player->sendMessage(Main::format("Gracz §4$nick §7odrzucil twoja prosbe o teleportacje"));
	    }
	}
}