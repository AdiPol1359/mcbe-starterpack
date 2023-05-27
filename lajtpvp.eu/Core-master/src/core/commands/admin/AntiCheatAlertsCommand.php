<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;

class AntiCheatAlertsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("anticheatalerts", "", true, false, ["aca"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(!($user = Main::getInstance()->getUserManager()->getUser($sender->getName())))
            return;

        $user->setAntiCheatAlerts(!$user->hasAntiCheatAlerts());
        $sender->sendMessage(MessageUtil::format("Powiadomienia z anticheata zostaly ".($user->hasAntiCheatAlerts() ? "§aWLACZONE" : "§cWYLACZONE")));
    }
}