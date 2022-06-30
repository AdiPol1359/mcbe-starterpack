<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\fakeinventory\inventory\shop\ParticleShopInventory;
use pocketmine\command\CommandSender;

class ParticlesCommand extends BaseCommand {
    public function __construct() {
        parent::__construct("particles", "Particles Command", false, false, "Komenda sklep sluzy do otwierania menu partcilesow", ["particle"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        //$player->sendForm(new ParticleShop());
        (new ParticleShopInventory($player))->openFor([$player]);
    }
}