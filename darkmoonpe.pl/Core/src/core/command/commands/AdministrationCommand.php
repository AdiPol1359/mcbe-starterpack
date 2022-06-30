<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\form\forms\AdministrationForm;
use pocketmine\command\CommandSender;

class AdministrationCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("administration", "Administration Command", false, false, "Komenda administracja sluzy do otwierania menu z informacjami o administracji!", ["administracja", "adma", "admins"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        $player->sendForm(new AdministrationForm());
    }
}