<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;
use pocketmine\network\mcpe\protocol\ActorEventPacket;

class KordyCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("kordy", "Komenda kordy", false);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

	    //$sender->sendMessage("§8§l>§r §7Twoje kordy: X: §4{$sender->getFloorX()} §7Y: §4{$sender->getFloorY()} §7Z: §4{$sender->getFloorZ()}");
	    
	    $pk = new ActorEventPacket();
	    $pk->entityRuntimeId = $player->getId();
	    $pk->event = 2;
	    
	    $sender->dataPacket($pk);
	    $sender->sendMessage("ok");
   }
}