<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\ManageServerInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ServerCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("server", "", true, false);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        (new ManageServerInventory())->openFor([$sender]);
    }
}