<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender, ConsoleCommandSender
};

use Core\Main;

class GamemodeCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("gamemode", "Komenda gamemode", true, ["gm"]);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
		    return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /gamemode §8(§40 §7| §41 §7| §42 §7| §43§8)"));
			return;
		}

		$player = $sender;

		if(isset($args[1])) {
            $player = $sender->getServer()->getPlayer($args[1]);

            if($player == null) {
        	    $sender->sendMessage("§8§l>§r §7Ten gracz jest §coffline§7!");
        	    return;
        	}
        }

        if($player instanceof ConsoleCommandSender) {
            $player->sendMessage("§8§l>§r §7Tej komendy mozesz uzyc tylko w grze!");
            return;
        }

		switch($args[0]) {
		    case 0:
                $player->setGamemode(0);
                $player->sendMessage("§8§l>§r §7Ustawiono tryb gry na §l§4SURVIVAL§r§7!");

                if(!$player === $sender || $sender  instanceof ConsoleCommandSender)
                    $sender->sendMessage("§8§l>§r §7Pomyslnie ustawiono tryb gry §4§lSURVIVAL§r §7dla gracza §4{$player->getName()}§7!");
		    break;

		    case 1:
                $player->setGamemode(1);
                $player->sendMessage("§8§l>§r §7Ustawiono tryb gry na §l§4CREATIVE§r§7!");

                if(!$player === $sender || $sender  instanceof ConsoleCommandSender)
                    $sender->sendMessage("§8§l>§r §7Pomyslnie ustawiono tryb gry §4§lCREATIVE§r §7dla gracza §4{$player->getName()}§7!");
		    break;

		    case 2:
                $player->setGamemode(2);
               $player->sendMessage("§8§l>§r §7Ustawiono tryb gry na §l§4ADVENTURE§r§7!");

                if(!$player === $sender || $sender  instanceof ConsoleCommandSender)
                    $sender->sendMessage("§8§l>§r §7Pomyslnie ustawiono tryb gry §4§lADVENTURE§r §7dla gracza §4{$player->getName()}§7!");
		    break;

		    case 3:
                $player->setGamemode(3);
               $player->sendMessage("§8§l>§r §7Ustawiono tryb gry na §l§4SPECTATOR§r§7!");

                if(!$player === $sender || $sender instanceof ConsoleCommandSender)
                    $sender->sendMessage("§8§l>§r §7Pomyslnie ustawiono tryb gry §4§lSPECTATOR§r §7dla gracza §4{$player->getName()}§7!");
		    break;

		    default:
			$sender->sendMessage(Main::format("Poprawne uzycie: /gamemode §8(§40 §7| §41 §7| §42 §7| §43§8)"));
		}
	}
}