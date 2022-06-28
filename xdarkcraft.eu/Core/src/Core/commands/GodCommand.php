<?php

namespace Core\commands;

use pocketmine\command\{
	Command, CommandSender, ConsoleCommandSender
};

use Core\Main;

class GodCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("god", "Komenda god", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		$player = $sender;

		if(isset($args[0])) {
            $player = $sender->getServer()->getPlayer($args[0]);

            if($player == null) {
        	    $sender->sendMessage("§8§l>§r §7Ten gracz jest §coffline§7!");
                return;
        	}
        }

        if($player instanceof ConsoleCommandSender) {
            $player->sendMessage("§8§l>§r §7Tej komendy mozesz uzyc tylko w grze!");
            return;
        }

        if(!isset(Main::$god[$player->getName()])) {
            Main::$god[$player->getName()] = true;

            if(!$player === $sender || $sender instanceof ConsoleCommandSender)
                $sender->sendMessage("§8§l>§r §7Pomyslnie wlaczono niesmiertelnosc graczu §4{$player->getName()}§7!");

            $player->sendMessage("§8§l>§r §7Niesmiertelnosc zostala §4wlaczona§7!");
        } else {
             unset(Main::$god[$player->getName()]);

             if(!$player === $sender || $sender instanceof ConsoleCommandSender)
                 $sender->sendMessage("§8§l>§r §7Pomyslnie wylaczono niesmiertelnosc graczu §4{$player->getName()}§7!");

             $player->sendMessage("§8§l>§r §7Niesmiertelnosc zostala §cwylaczona§7!");
        }
    }
}