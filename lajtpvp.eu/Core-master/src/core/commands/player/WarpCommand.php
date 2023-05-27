<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\WarpInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class WarpCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("warp", "", false, false);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        (new WarpInventory())->openFor([$sender]);
    }
}