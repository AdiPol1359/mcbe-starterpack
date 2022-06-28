<?php

namespace Core\commands;

use pocketmine\{
	Server, Player
};

use pocketmine\command\{
	Command, CommandSender
};

use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

use Core\Main;

use Core\form\Form;

class PcaseCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("pcase", "Komenda pcase", false);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
		    return;

		if(empty($args)) {
			$sender->sendMessage(Main::format("Poprawne uzycie: /pcase §8(§4ilosc§8) (§4gracz§8)"));
			return;
		}

		if(!is_numeric($args[0])) {
		$sender->sendMessage(Main::format("Poprawne uzycie: /pcase §8(§4ilosc§8) (§4gracz§8)"));
			return;
		}

		$player = isset($args[1]) ? Server::getInstance()->getPlayer($args[1]) : $sender;

		if(!$player instanceof Player) {
			$sender->sendMessage("§8§l>§r §7Ten gracz jest §4offline§7!");
			return;
		}

		$item = Item::get(146, 0, $args[0]);
		$item->setCustomName("§r§l§9PremiumCase");
		$item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));

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

		 $sender->sendMessage(Main::format("Pomyslnie dano §4{$args[0]} §7PremiumCase graczowi §4{$player->getName()}"));
	}
}