<?php

namespace NicePE_Core;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use NicePE_Core\Main;

class SimpleMessagesTask extends PluginTask{

    public function __construct(Main $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun($currentTick){
        $this->getOwner();
        $this->plugin->configFile = $this->owner->getConfig()->getAll();
        $messages = $this->plugin->configFile["messages"];
        $messagekey = array_rand($messages, 1);
        $message = $messages[$messagekey];
        $this->owner->getServer()->broadcastMessage($this->plugin->configFile["color"]."§8• (".$this->plugin->configFile["prefix"].") ".$message);
    }

}
