<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

use Core\Main;

use Core\form\Form;

class PallCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("pall", "Komenda pall", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
        return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /pall §8(§4ilosc§8)"));
			return;
		}

		if(!is_numeric($args[0])) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /pall §8(§4ilosc§8)"));
			return;
		}

		$item = Item::get(146, 0, $args[0]);
		$item->setCustomName("§r§l§9PremiumCase");
		$item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));

		foreach(Server::getInstance()->getOnlinePlayers() as $player) {
			 if($player->getInventory()->canAddItem($item))
		  $player->getInventory()->addItem($item);
		 else {
			 $count = $item->getCount();
			
		 	$stacks = floor($count / 64);
	 		$rest = $count - ($stacks * 64);
			
		 	for($i = 1; $i <= $stacks; $i++) {
		 		$item = $item->setCount(64);
				
		 		if($player->getInventory()->canAddItem($item))
				  $player->getInventory()->addItem($item);
		   else
		    $player->getLevel()->dropItem($player->asVector3(), $item);
			 }
			 
			 $player->getLevel()->dropItem($player->asVector3(), $item->setCount($rest));
		 }
		}
		
   if($args[0] == 0){
   			 $sender->sendMessage(Main::format("Caly serwer otrzmal §4{$args[0]} §7PremiumCase'ów!"));
   }
   elseif($args[0] == 1){
   			 $sender->sendMessage(Main::format("Caly serwer otrzmal §4{$args[0]} §7PremiumCase!"));
   }
   elseif($args[0]<=4){
   			 $sender->sendMessage(Main::format("Caly serwer otrzmal §4{$args[0]} §7PremiumCase'y!"));
   }
   else{
   			 $sender->sendMessage(Main::format("Caly serwer otrzmal §4{$args[0]} §7PremiumCase'ów!"));
   }
	}
}