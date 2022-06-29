<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\fakeinventory\inventory\shop\PetShopInventory;
use pocketmine\command\CommandSender;

class PetCommand extends BaseCommand {
    public function __construct() {
        parent::__construct("pet", "pet Command", false, false, "Komenda sklep sluzy do otwierania menu zwierzakow", ["pets", "zwierzak", "zwierzaki"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        (new PetShopInventory($player))->openFor([$player]);
    }
}