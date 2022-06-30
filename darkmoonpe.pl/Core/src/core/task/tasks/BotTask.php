<?php

namespace core\task\tasks;

use core\manager\managers\SettingsManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class BotTask extends Task {

    public function onRun($currentTick) {

        $message = ConfigUtil::BOT_MESSAGES[rand(0, count(ConfigUtil::BOT_MESSAGES) - 1)];

        foreach(Server::getInstance()->getOnlinePlayers() as $p) {

            if(UserManager::getUser($p->getName()) === null)
                return;

            if(!UserManager::getUser($p->getName())->isSettingEnabled(SettingsManager::BOT_NOTIFICATION))
                return;

            $p->sendMessage($message);
        }
    }
}