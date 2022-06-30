<?php

namespace core\task\tasks;

use core\Main;
use core\manager\managers\ScoreboardManager;
use core\manager\managers\SettingsManager;
use core\user\UserManager;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class ScoreboardTask extends Task {

    public function onRun($currentTick) : void {

        foreach(Main::$sb as $nick => $var) {

            $player = Server::getInstance()->getPlayerExact($nick);

            if($player === null)
                continue;

            if(!UserManager::getUser($player->getName())->isSettingEnabled(SettingsManager::SCOREBOARD))
                ScoreboardManager::removeScoreboard($player);
            else
                ScoreboardManager::sendScoreboard($player);
        }
    }
}