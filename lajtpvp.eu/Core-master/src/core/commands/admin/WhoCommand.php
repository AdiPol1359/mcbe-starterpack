<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;

class WhoCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("who", "", true, false);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

        if(!$user)
            return;

        $user->setLastData(Settings::$WHO, (time() + Settings::$WHO_TIME), Settings::$TIME_TYPE);
        $sender->sendMessage(MessageUtil::format("Kliknij na gracza ktorego nick chcesz sprawdzic, masz na to §e15 §7sekund"));
    }
}