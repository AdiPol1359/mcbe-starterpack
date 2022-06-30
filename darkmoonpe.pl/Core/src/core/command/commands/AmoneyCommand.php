<?php

namespace core\command\commands;

use core\command\BaseCommand;
use pocketmine\command\CommandSender;
use core\form\forms\AmoneyForm;

class AmoneyCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("apieniadze", "Apieniadze Command", true, false, "Komenda Amoney umozliwia zarzadzaniem pieniedzmi gracza przez administratora", ["acash", "apieniadze", "amoney"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        $player->sendForm(new AmoneyForm());
    }
}