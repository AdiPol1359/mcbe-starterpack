<?php

namespace Core\commands;

use pocketmine\command\{
	Command, CommandSender, ConsoleCommandSender
};

use Core\Main;

class ClearCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("clear", "Komenda clear", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender)) return;

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

        if($player !== $sender || $sender  instanceof ConsoleCommandSender)
            $sender->sendMessage(Main::format("Pomyslnie wyczyszczono ekwipunek gracza §4{$player->getName()}§7!"));

        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();

        $player->sendMessage("§8§l>§r §7Twoj ekwipunek zostal wyczyszczony!");
	}
}