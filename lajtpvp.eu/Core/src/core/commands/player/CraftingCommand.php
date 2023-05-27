<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\CraftingInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CraftingCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("crafting", "", false, false, ["craftingi"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        (new CraftingInventory())->openFor([$sender]);
    }
}