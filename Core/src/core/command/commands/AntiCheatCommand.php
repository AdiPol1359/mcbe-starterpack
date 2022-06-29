<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\form\forms\AntiCheatForm;
use pocketmine\command\CommandSender;

class AntiCheatCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("anticheat", "AntiCheat Command", true, false, "Ta komenda sluzy do zarzadzania modulami antycheata", ["ac"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        $player->sendForm(new AntiCheatForm());
    }
}