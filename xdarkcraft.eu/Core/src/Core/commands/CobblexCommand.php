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

class CobblexCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("cobblex", "Komenda cobblex", false, ["cx"]);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}

		if(!$sender->getInventory()->contains(Item::get(4, 0, 9 * 64))) {
			$sender->sendMessage("§8§l>§r §7Aby zakupic CobbleX potrzebujesz §49§7x§464 §7cobblestone!");
			return;
		}

		$item = Item::get(48);
		$item->setCustomName("§r§l§4CobbleX");
		$item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));

		$sender->getInventory()->removeItem(Item::get(4, 0, 9 * 64));
		$sender->getInventory()->addItem($item);

		$sender->sendMessage("§8§l>§r §7Pomyslnie zakupiono §4CobbleX§7!");
	}
}
