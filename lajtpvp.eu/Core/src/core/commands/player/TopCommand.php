<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\TopInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TopCommand extends BaseCommand{
    public function __construct() {
        parent::__construct("top", "", false, false,["topka"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        (new TopInventory())->openFor([$sender]);
    }
}