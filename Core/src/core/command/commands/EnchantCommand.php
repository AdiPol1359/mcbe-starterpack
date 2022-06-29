<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\fakeinventory\inventory\EnchantInventory;
use pocketmine\command\CommandSender;

class EnchantCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("enchanty", "Enchanty Command", true, false, "Komenda enchanty sluzy do kupowania enchantow na dany item", ["ench", "enchants", "enchant"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        (new EnchantInventory($player, 24))->openFor([$player]);
    }
}