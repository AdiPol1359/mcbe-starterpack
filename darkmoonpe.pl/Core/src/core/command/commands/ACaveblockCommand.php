<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\form\forms\acaveblock\ACaveblockMainForm;
use pocketmine\command\CommandSender;

class ACaveblockCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("acaveblock", "ACaveBlock Command", true, false, "Komenda acaveblock sluzy do zarzadzania jaskinia innych graczy, jej permisjami, czlonkami itd.", ["acave", "acb", "cba"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        $player->sendForm(new ACaveblockMainForm());
    }
}