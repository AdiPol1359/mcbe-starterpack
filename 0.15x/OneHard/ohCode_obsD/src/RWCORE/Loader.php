<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 27/11/2016
 * Time: 20:03
 */

namespace RWCORE;


use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use RWCORE\ObsidianBreaker\OBListener;

class Loader extends PluginBase implements Listener
{

    const Prefix = "";

    public function onEnable()
    {
        $cmd = [
            new Enviar($this),
            new Reparar($this),
        ];

        $this->saveDefaultConfig();

        $this->data = new Config($this->getDataFolder() . "obsidian.json", Config::JSON);

        $this->getServer()->getPluginManager()->registerEvents(new OBListener($this), $this);

        $this->getServer()->getCommandMap()->registerAll("RWCORE", $cmd);

    }

    public function getData(){
        return $this->data;
    }

}