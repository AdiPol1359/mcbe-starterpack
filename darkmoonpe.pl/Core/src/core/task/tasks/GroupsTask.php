<?php

namespace core\task\tasks;

use core\Main;
use pocketmine\scheduler\Task;

class GroupsTask extends Task {

    public function onRun(int $currentTick) {
        Main::getProvider()->taskProccess();
    }
}