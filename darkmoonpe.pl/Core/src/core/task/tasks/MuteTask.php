<?php

namespace core\task\tasks;

use core\Main;
use core\manager\managers\MuteManager;
use pocketmine\scheduler\Task;

class MuteTask extends Task {

    public function onRun($currentTick) {
        $result = Main::getDb()->query("SELECT * FROM mute");

        while($row = $result->fetchArray()) {

            $a_date = date("d.m.Y H:i:s");

            if(strtotime($a_date) > strtotime($row['time']))
                MuteManager::unMute($row["nick"]);
        }
    }
}