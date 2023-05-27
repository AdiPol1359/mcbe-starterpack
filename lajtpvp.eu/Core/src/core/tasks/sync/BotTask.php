<?php

declare(strict_types=1);

namespace core\tasks\sync;

use core\utils\MessageUtil;
use core\utils\Settings;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class BotTask extends Task {

    public function onRun() : void {
        $message = MessageUtil::format(Settings::$BOT_MESSAGES[mt_rand(0, count(Settings::$BOT_MESSAGES) - 1)]);

        foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            $onlinePlayer->sendMessage($message);
        }
    }
}