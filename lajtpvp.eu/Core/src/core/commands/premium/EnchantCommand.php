<?php

declare(strict_types=1);

namespace core\commands\premium;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\EnchantInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EnchantCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("enchanty", "", true, false, ["ench", "enchants", "enchant"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        (new EnchantInventory(24))->openFor([$sender]);
    }
}