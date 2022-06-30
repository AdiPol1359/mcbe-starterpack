<?php

namespace core\command\commands;

use core\command\BaseCommand;
use pocketmine\command\CommandSender;

use core\form\forms\MoneyForm;

class MoneyCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("pieniadze", "Pieniadze Command", false, false, "Komenda pieniadze sluzy do zarzadzania stanem konta", ['money', 'cash', 'mymoney', 'stankonta']);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        $player->sendForm(new MoneyForm($player));
    }
}