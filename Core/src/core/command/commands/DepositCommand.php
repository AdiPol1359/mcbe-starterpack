<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\form\forms\DepositForm;
use pocketmine\command\CommandSender;

class DepositCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("deposit", "Deposit Command", false, true, "Komenda sluzy do otwierania menu z depozytem koxow, refow, perel", ["depozyt", "schowek"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        $player->sendForm(new DepositForm($player));
    }
}