<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\fakeinventory\inventory\ServerInventory;
use pocketmine\command\CommandSender;

class ServerCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("server", "Server Command", true, false, "Ta komenda sluzy do zarzadzania waznymi funkcjami serwera", ["serv", "manage"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        (new ServerInventory($player))->openFor([$player]);
    }
}