<?php
/**
 * User: Michael Leahy
 * Date: 6/24/14
 * Time: 11:02 AM
 */

namespace SimpleMessages;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class SimpleMessages extends PluginBase{

    public $configFile;

    public function onEnable(){
        @mkdir($this->getDataFolder());
        $this->configFile = (new Config($this->getDataFolder()."config.yml", Config::YAML, array(
            "messages" => array(
                "Listę komend znajdziesz pod /help i informacje na spawn •",
                "Chcesz  teleportować się do>/ Warp  •",
                "Kompas Wzkazuje kord spawnu •",
                "Tryp servera to Harcor walcz albo gin •",
            ),
            "time" => "60",
            "prefix" => "LizuCraft",
            "color" => "§o§l§a"
        )))->getAll();

        $time = intval($this->configFile["time"]) * 20;
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new SimpleMessagesTask($this), $time);

        $this->getLogger()->info("I've been enabled!");
    }

    public function onDisable(){
        $this->getLogger()->info("I've been disabled!");
    }

}
