<?php

namespace core\task\tasks;

use core\user\UserManager;
use pocketmine\scheduler\Task;

class QuestTask extends Task {

    public function onRun(int $currentTick) {
        foreach(UserManager::getUsers() as $user) {

            if($user->getTimestamp() > time())
                continue;

            $user->setTimestamp((time() + 86400));
            $user->resetNotBeingProcessed();
            $user->generateQuests(5);
        }
    }
}