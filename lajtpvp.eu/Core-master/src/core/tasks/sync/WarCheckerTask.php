<?php

declare(strict_types=1);

namespace core\tasks\sync;

use core\Main;
use pocketmine\scheduler\Task;

class WarCheckerTask extends Task {

    public function onRun() : void {
        foreach(Main::getInstance()->getWarManager()->getWars() as $war) {
            if($war->hasEnded())
                continue;

            if($war->getEndTime() < time())
                $war->endWar();
        }
    }
}