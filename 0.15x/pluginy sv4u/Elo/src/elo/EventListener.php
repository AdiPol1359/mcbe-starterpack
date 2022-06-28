<?php

namespace elo;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use elo\Elo;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;

Class EventListener implements Listener{

    private $deathloss;
    private $main;

	public function __construct(Elo $main) {
		$this->main = $main;
                $this->deathloss = $this->main->getConfig()->get("Death-Elo-Loss");
    }

     public function onDeath(PlayerDeathEvent $ev){
         if($this->main->config->get("Lose-Elo-onDeath") === true){
			 		switch(mt_rand(1, 11)){
					 case 1:
					 					 case 1:
					 $eloloss = 30;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 break;
					 					 case 2:
					 $eloloss = 29;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 break;
					 					 case 3:
					 $eloloss = 28;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 break;
					 					 case 4:
					 $eloloss = 27;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 break;
					 					 case 5:
					 $eloloss = 26;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 break;
					 					 case 6:
					 $eloloss = 25;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 break;
					 					 case 7:
					 $eloloss = 24;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 break;
					 					 case 8:
					 $eloloss = 23;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 break;
					 					 case 9:
					 $eloloss = 22;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 break;
					 					 case 10:
					 $eloloss = 21;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 break;
					 case 11:
					 $eloloss = 20;
					 $this->main->removeElo($ev->getPlayer()->getName(), $eloloss);
					 break;
         }
		 }
         if($this->main->config->get("Get-Elo-On-Kill") === true){
             $cause = $ev->getPlayer()->getLastDamageCause();
             if($cause instanceof EntityDamageByEntityEvent and $cause->getDamager() instanceof Player){
              $killername = $cause->getDamager()->getName();
				 switch(mt_rand(1, 16)){
					 case 1:
					 $elo = 35;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 2:
					 $elo = 34;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 3:
					 $elo = 33;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 4:
					 $elo = 32;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 5:
					 $elo = 31;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 6:
					 $elo = 30;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 7:
					 $elo = 29;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 8:
					 $elo = 28;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 9:
					 $elo = 27;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 10:
					 $elo = 26;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 11:
					 $elo = 25;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 12:
					 $elo = 24;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 13:
					 $elo = 23;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 14:
					 $elo = 22;
					 $this->main->addElo($killername, $elo);
					 break;
					 					 case 15:
					 $elo = 21;
					 $this->main->addElo($killername, $elo);
					 break;
					 case 16:
					 $elo = 20;
					 $this->main->addElo($killername, $elo);
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
