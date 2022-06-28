<?php

namespace Core\commands;

use Core\form\EnchantArmorForm;
use Core\form\EnchantBowForm;
use Core\form\EnchantSwordForm;
use Core\form\EnchantToolsForm;
use pocketmine\Player;
use pocketmine\item\{
    Tool, Armor, Sword, ChainBoots, DiamondBoots, GoldBoots, IronBoots, LeatherBoots
};
use Core\item\Bow;
use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

use Core\form\KitsForm;

class EnchantCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("enchant", "Komenda enchant", true, ["enchanting"]);
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}

		$item = $sender->getInventory()->getItemInHand();

        switch(true) {
            case $item instanceof Sword:
                $sender->sendForm(new EnchantSwordForm(24));
                break;

            case $item instanceof Bow:
                $sender->sendForm(new EnchantBowForm(24));
                break;

            case $item instanceof Tool:
                $sender->sendForm(new EnchantToolsForm(24));
                break;

            case $item instanceof Armor:
                if($item instanceof ChainBoots || $item instanceof DiamondBoots || $item instanceof GoldBoots || $item instanceof IronBoots || $item instanceof LeatherBoots)
                    $sender->sendForm(new EnchantArmorForm(24, true));
                else
                    $sender->sendForm(new EnchantArmorForm(24));
                break;

            case $item->getId() == 0:
                $sender->sendMessage("§8§l>§r §7Nie mozesz §4zenchantowac §7tego itemu!");
                break;

            default:
                $sender->sendMessage("§8§l>§r §7Nie mozesz §4zenchantowac §7tego itemu!");
        }
	}
}