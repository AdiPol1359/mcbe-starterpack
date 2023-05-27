<?php

declare(strict_types=1);

namespace core\tasks\sync;

use core\Main;
use pocketmine\scheduler\Task;

class UpdateUserTask extends Task {

    public function onRun() : void {
        foreach(Main::getInstance()->getUserManager()->getUsers() as $user) {
            if($user->isConnected())
                $user->onUpdate();
        }
    }
}