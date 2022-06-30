<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\fakeinventory\inventory\DropInventory;
use pocketmine\command\CommandSender;

class DropCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("drop", "Drop Command", false, false, "Komenda drop sluzy do otwierania menu z wyborem dropu z stone'a lub cobbla");
    }

    public function onCommand(CommandSender $player, array $args) : void {
        (new DropInventory($player))->openFor([$player]);
    }
}