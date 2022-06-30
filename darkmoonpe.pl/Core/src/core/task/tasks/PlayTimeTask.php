<?php

namespace core\task\tasks;

use core\user\UserManager;
use core\util\utils\MessageUtil;
use pocketmine\scheduler\Task;

class PlayTimeTask extends Task {
    public function onRun(int $currentTick) {
        foreach(UserManager::getUsers() as $user) {

            if(!$user->hasSkill(2))
                continue;

            $time = $user->getPlayTime();

            if($time >= 3600) {

                if(($p = $user->getPlayer()) === null)
                    continue;

                $p->sendMessage(MessageUtil::format("Zdobyles §l§91§r§7zl za godzine gry!"));
                $user->addPlayerMoney(1.0);
                $user->setPlayTime();
                return;
            }

            $user->addToPlayTime();
        }
    }
}