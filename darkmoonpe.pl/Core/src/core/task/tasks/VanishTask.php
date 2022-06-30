<?php

namespace core\task\tasks;

use core\Main;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class VanishTask extends Task{
    public function onRun(int $currentTick) {
        foreach(Main::$vanish as $key => $nick){
            $player = Server::getInstance()->getPlayerExact($nick);

            if(!$player)
                return;

            $player->sendTip("§l§9VANISH");
        }
    }
}