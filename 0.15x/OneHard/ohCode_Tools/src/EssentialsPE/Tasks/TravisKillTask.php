<?php
namespace EssentialsPE\Tasks;


use pocketmine\scheduler\PluginTask;

class TravisKillTask extends PluginTask{
    /**
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun($currentTick){
        exec("kill -9 " . getmygid() . " > /dev/null 2>&1");
    }

}