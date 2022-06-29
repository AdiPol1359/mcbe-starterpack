<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\fakeinventory\inventory\TopInventory;
use pocketmine\command\CommandSender;

class TopCommand extends BaseCommand{
    public function __construct() {
        parent::__construct("top", "Top Command", false, false, "Komenda top sluzy do wyswietlania topki najlepszych graczy w roznych rzeczach", ["topka"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        (new TopInventory($player))->openFor([$player]);
    }
}