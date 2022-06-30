<?php

namespace core\task\tasks;

use core\Main;
use pocketmine\scheduler\Task;

class TpaTask extends Task{
    public function onRun(int $currentTick) {
        foreach(Main::$tp as $nick => $time){
            foreach($time as $tpPlayer => $value)
                if($value <= time())
                    unset(Main::$tp[$nick][$tpPlayer]);
        }
    }
}