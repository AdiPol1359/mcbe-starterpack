<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};

use pocketmine\item\Item;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

use Core\Main;

class StoniarkaCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("stoniarka", "Komenda stoniarka", true);
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
		    return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}
		
		if(empty($args)) {
			$sender->sendMessage(Main::format("Uzyj /stoniarka (0.5, 1.5, 3)"));
			return;
		}
		
		$name = "§r§7Generator Kamienia§4";
		
		switch($args[0]) {
			case "0.5":
			 $name .= " 0.5s";
			break;
			
			case "1.5":
			 $name .= " 1.5s";
			break;
			
			case "3":
			 $name .= " 3s";
			break;
		}
		
		$item = Item::get(1);
		$item->setCustomName($name);
		$item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));
		
		$sender->getInventory()->addItem($item);
		
		$sender->sendMessage(Main::format("Otrzymano stoniarke"));
	}
}