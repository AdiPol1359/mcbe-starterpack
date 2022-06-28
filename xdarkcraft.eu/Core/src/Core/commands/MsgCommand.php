<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};
use pocketmine\level\sound\ClickSound;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use Core\Main;

class MsgCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("msg", "Komenda msg");
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}
		
		if(!isset($args[1])) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /msg §8(§4nick§8) (§4wiadomosc§8)"));
			return;
		}
		
		$player = $sender->getServer()->getPlayer(array_shift($args));
		$msg = trim(implode(" ", $args));
		
		if(!$player) {
			$sender->sendMessage("§8§l>§r §7Ten gracz jest §4offline§7!");
			return;
		}
		
		$sender->sendMessage("§4Ja §8§l>§r §4{$player->getName()}§8: §7$msg");
		$player->sendMessage("§4{$sender->getName()} §8§l>§r §4Ja§8: §7$msg");
        $player->getLevel()->addSound(new ClickSound($player), [$player]);
		
		Main::$msgR[$sender->getName()] = $player->getName();
		Main::$msgR[$player->getName()] = $sender->getName();
	}
}