<?php

namespace NicePE_Core;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class BlockCmdListener implements Listener{

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function onCmd(PlayerCommandPreprocessEvent $event){
        $p = $event->getPlayer();
        $msg = $event->getMessage();
        if($p->hasPermission('block.cmd.pe.bypass')){
            return;
        }
        foreach($this->plugin->getCmd() as $cmd){
            if(stripos($msg, $cmd) === 0){
                $event->setCancelled(true);
                $p->sendMessage($this->plugin->getMsg());
            }
        }
    }

}
