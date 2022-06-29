<?php

namespace core\task\tasks;

use core\Main;
use core\manager\managers\BanManager;
use pocketmine\scheduler\Task as PluginTask;

class BanTask extends PluginTask {

    public function onRun($currentTick) {
        $result = Main::getDb()->query("SELECT * FROM ban");

        while($row = $result->fetchArray()) {

            $a_date = date("d.m.Y H:i:s");

            if(strtotime($a_date) > strtotime($row['time']))
                BanManager::unBan($row["nick"]);
        }
    }
}