<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\fakeinventory\inventory\SettingsInventory;
use pocketmine\command\CommandSender;

class SettingsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("ustawienia", "Ustawienia Command", false, false, "Komenda settings sluzy do otwierania menu ustawien gracza", ['settings', 'opcje']);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        (new SettingsInventory($player))->openFor([$player]);
    }
}