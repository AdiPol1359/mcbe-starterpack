<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\fakeinventory\inventory\hazard\HazardInventory;
use pocketmine\command\CommandSender;

class HazardCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("hazard", "Hazard Command", false, false, "Komenda drop sluzy do otwierania menu z grami hazardowymi");
    }

    public function onCommand(CommandSender $player, array $args) : void {
        (new HazardInventory($player))->openFor([$player]);
    }
}