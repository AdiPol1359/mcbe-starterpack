<?php

namespace elo;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use elo\Elo;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\{Command, CommandSender};
use pocketmine\Player;
use pocketmine\Server;
use xSmoothy\FactionsPro\FactionMain;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;

Class EventListener implements Listener{

    private $deathloss;
    private $main;
		    
			public function getAPI()
    {
        return Server::getInstance()->getPluginManager()->getPlugin("FactionsPro");
    }

	public function __construct(Elo $main) {
		$this->main = $main;
                $this->deathloss = $this->main->getConfig()->get("Death-Elo-Loss");
    }
	
     public function onDeath(PlayerDeathEvent $ev){
         if($this->main->config->get("Lose-Elo-onDeath") === true){
		 }
         if($this->main->config->get("Get-Elo-On-Kill") === true){
             $cause = $ev->getPlayer()->getLastDamageCause();
             if($cause instanceof EntityDamageByEntityEvent and $cause->getDamager() instanceof Player){
              $killername = $cause->getDamager()->getName();
				 switch(mt_rand(1, 14)){
					 case 1:
					 $eloloss = 30;
					 $elo = 35;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					 					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");

 Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
					 					 case 2:
					 $eloloss = 27;
					 $elo = 38;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					  Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
					 					 case 3:
					 $eloloss = 31;
					 $elo = 42;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					  Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
					 					 case 4:
					 $eloloss = 29;
					 $elo = 45;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					  Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
					 					 case 5:
					 $eloloss = 89;
					 $elo = 170;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					  Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
					 					 case 6:
					 $eloloss = 12;
					 $elo = 6;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					  Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
					 					 case 7:
					 $eloloss = 69;
					 $elo = 123;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					  Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
					 case 8:
					 $eloloss = 18;
					 $elo = 49;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					  Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
					 case 9:
					 $eloloss = 98;
					 $elo = 48;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					  Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
					 case 10:
					 $eloloss = 36;
					 $elo = 12;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					 Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8] §8•");
					 break;
					 case 11:
					 $eloloss = 37;
					 $elo = 43;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					  Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
					 case 12:
					 $eloloss = 61;
					 $elo = 89;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					  Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
					 case 13:
					 $eloloss = 54;
					 $elo = 76;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					  Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
					 case 14:
					 $eloloss = 55;
					 $elo = 85;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 $this->main->addElo($killername, $elo);
					  
$cause->getDamager()->getPlayer()->sendTip("§l§7               Zabiles §6" . $ev->getPlayer()->getName() . " §8[§a+" . $elo . "§8]§r\n§r§7Gracz §e" . $ev->getPlayer()->getName() . " §8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . " §8[§a+" . $elo . "§8]\n\n\n\n\n");
					  Server::getInstance()->broadcastMessage("§8• §7Gracz §e" . $ev->getPlayer()->getName() ."§8[§c-" . $eloloss . "§8] §7zostal zabity przez §6" . $killername . "§8[§a+" . $elo . "§8] •");
					 break;
				 }
             }
         }
     }

    public function onJoin(PlayerJoinEvent $ev){
        if(!($this->main->eloyaml->exists($ev->getPlayer()->getName()))){
            $this->main->createDataFor($ev->getPlayer()->getName());
        }
    }
}
