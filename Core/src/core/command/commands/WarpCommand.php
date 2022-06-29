<?php

namespace core\command\commands;

use core\command\BaseCommand;
use pocketmine\command\CommandSender;

use core\form\forms\WarpForm;

class WarpCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("warp", "Warp Command", false, false, "Komenda warp sluzy do wyswietlania serwerowych warpow");
    }

    public function onCommand(CommandSender $player, array $args) : void {
        $player->sendForm(new WarpForm());
    }
}