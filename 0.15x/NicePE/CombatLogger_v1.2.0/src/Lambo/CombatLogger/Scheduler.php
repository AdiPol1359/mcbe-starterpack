<?php
namespace Lambo\CombatLogger;

use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class Scheduler extends PluginTask{

    public function __construct($plugin){
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun($currentTick){
        foreach($this->plugin->players as $player=>$time){
                $p = $this->plugin->getServer()->getPlayer($player);
                if($p instanceof Player){
				    if((time() - $time) > $this->plugin->interval){
					$p->sendPopup("§l§aANTYLOGOUT§r");
                    unset($this->plugin->players[$player]);
					}
					if((time() - $time) == 1){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c30s §7[§8||||||||||||||||||||||||||||||§7]");
					}
					if((time() - $time) == 2){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c29s §7[§c|§8|||||||||||||||||||||||||||||§7]");
					}
					if((time() - $time) == 3){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c28s §7[§c||§8||||||||||||||||||||||||||||§7]");
					}
					if((time() - $time) == 4){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c27s §7[§c|||§8|||||||||||||||||||||||||||§7]");
					}
					if((time() - $time) == 5){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c26s §7[§c||||§8||||||||||||||||||||||||||§7]");
					}
					if((time() - $time) == 6){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c25s §7[§c|||||§8|||||||||||||||||||||||||§7]");
					}
					if((time() - $time) == 7){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c24s §7[§c||||||§8||||||||||||||||||||||||§7]");
					}
					if((time() - $time) == 8){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c23s §7[§c|||||||§8|||||||||||||||||||||||§7]");
					}
					if((time() - $time) == 9){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c22s §7[§c||||||||§8||||||||||||||||||||||§7]");
					}
					if((time() - $time) == 10){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c21s §7[§c|||||||||§8|||||||||||||||||||||§7]");
					}
					if((time() - $time) == 11){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c20s §7[§c||||||||||§8||||||||||||||||||||§7]");
					}
					if((time() - $time) == 12){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c19s §7[§c|||||||||||§8|||||||||||||||||||§7]");
					}
					if((time() - $time) == 13){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c18s §7[§c||||||||||||§8||||||||||||||||||§7]");
					}
					if((time() - $time) == 14){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c17s §7[§c|||||||||||||§8|||||||||||||||||§7]");
					}
					if((time() - $time) == 15){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c16s §7[§c||||||||||||||§8||||||||||||||||§7]");
					}
					if((time() - $time) == 16){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c15s §7[§c|||||||||||||||§8|||||||||||||||§7]");
					}
					if((time() - $time) == 17){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c14s §7[§c||||||||||||||||§8||||||||||||||§7]");
					}
					if((time() - $time) == 18){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c13s §7[§c|||||||||||||||||§8|||||||||||||§7]");
					}
					if((time() - $time) == 19){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c12s §7[§c||||||||||||||||||§8||||||||||||§7]");
					}
					if((time() - $time) == 20){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c11s §7[§c|||||||||||||||||||§8|||||||||||§7]");
					}
					if((time() - $time) == 21){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c10s §7[§c||||||||||||||||||||§8||||||||||§7]");
					}
					if((time() - $time) == 22){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c9s §7[§c|||||||||||||||||||||§8|||||||||§7]");
					}
					if((time() - $time) == 23){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c8s §7[§c||||||||||||||||||||||§8||||||||§7]");
					}
					if((time() - $time) == 24){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c7s §7[§c|||||||||||||||||||||||§8|||||||§7]");
					}
					if((time() - $time) == 25){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c6s §7[§c||||||||||||||||||||||||§8||||||§7]");
					}
					if((time() - $time) == 26){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c5s §7[§c|||||||||||||||||||||||||§8|||||§7]");
					}
					if((time() - $time) == 27){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c4s §7[§c||||||||||||||||||||||||||§8||||§7]");
					}
					if((time() - $time) == 28){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c3s §7[§c|||||||||||||||||||||||||||§8|||§7]");
					}
					if((time() - $time) == 29){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c2s §7[§c||||||||||||||||||||||||||||§8||§7]");
					}
					if((time() - $time) == 30){
			        $p->sendPopup("§l§4ANTY-LOGOUT§r §c1s §7[§c|||||||||||||||||||||||||||||§8|§7]");
					}
                }else unset($this->plugin->players[$player]);
            }
			}
        }