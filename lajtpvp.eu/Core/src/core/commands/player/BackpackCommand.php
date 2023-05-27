<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\BackpackInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class BackpackCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("backpack", "", false, false, ["plecak"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        (new BackpackInventory($sender))->openFor([$sender]);
    }
}