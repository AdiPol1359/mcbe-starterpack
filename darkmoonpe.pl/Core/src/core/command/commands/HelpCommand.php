<?php

namespace core\command\commands;

use core\command\BaseCommand;
use pocketmine\command\CommandSender;
use core\form\forms\HelpForm;

class HelpCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("pomoc", "Pomoc Command", false, false, "test");
    }

    public function onCommand(CommandSender $player, array $args) : void {
        $player->sendForm(new HelpForm($player));
    }
}