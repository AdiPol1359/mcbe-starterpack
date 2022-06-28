<?php
/**
 * User: Michael Leahy
 * Date: 6/23/14
 * Time: 6:38 PM
 */

namespace SimpleMessages;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SimpleMessages\SimpleMessages;

class SimpleMessagesTask extends PluginTask{

    public function __construct(SimpleMessages $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun($currentTick){
        $this->getOwner();
        $this->plugin->configFile = $this->owner->getConfig()->getAll();
        $messages = $this->plugin->configFile["messages"];
        $messagekey = array_rand($messages, 1);
        $message = $messages[$messagekey];
        $this->owner->getServer()->broadcastMessage($this->plugin->configFile["color"]."• [".$this->plugin->configFile["prefix"]."] ".$message);
    }

}
