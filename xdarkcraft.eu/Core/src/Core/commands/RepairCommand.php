<?php

namespace Core\commands;

use pocketmine\{
    Server, Player
};

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class RepairCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("repair", "Komenda repair", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

	        if(!$sender instanceof Player) {
            	$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
            	return;
            }

	        if(empty($args)) {
 		    $item = $sender->getInventory()->getItemInHand();
 	  	    $sender->getInventory()->setItemInHand($item->setDamage(0));
 	  	    $sender->sendMessage("§8§l>§r §7Pomyslnie item zostal naprawiony!");
 	  	    return;
 	  	}

 	  	switch($args[0]) {
 	  	    case "all":
 	  	        foreach($sender->getInventory()->getContents() as $slot => $item)
 	  	            $sender->getInventory()->setItem($slot, $item->setDamage(0));

 	  	        foreach($sender->getArmorInventory()->getContents() as $slot => $item)
                    $sender->getArmorInventory()->setItem($slot, $item->setDamage(0));

                $sender->sendMessage("§8§l>§r §7Pomyslnie naprawiono wszystkie przedmioty!");
 	  	    break;
 	  	    default:
 	  	        $sender->sendMessage(Main::format("Nieznany argument!"));
 	  	}
   }
}