<?php

namespace FactionsPro;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\block\Sand;
use pocketmine\block\Air;
use pocketmine\block\Obsidian;
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
use pocketmine\level\particle\LavaParticle;

class FactionListener implements Listener {
	
	public $plugin;
	private $address_ip = [];
	private $cooldown = [];
        
	public function __construct(FactionMain $pg) {
		$this->plugin = $pg;
	}
	
	public function factionChat(PlayerChatEvent $PCE) {
		
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
                $event->getPlayer()->sendPopup("§aNie mozesz zniszczyc serca gildii.");
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
	
	public function trueBoyFarmer(BlockPlaceEvent $event){
	 $blok = $event->getBlock();
	 $gracz = $event->getPlayer();
	 $y = $blok->getFloorY();
	 $x = $blok->getFloorX();
	 $z = $blok->getFloorZ();
	 if ($this->plugin->isInPlot($event->getBlock()->getFloorX(), $event->getBlock()->getFloorZ())) {
            if ($this->plugin->inOwnPlot($event->getPlayer(), $event->getBlock()->getFloorX(), $event->getBlock()->getFloorZ())) {
                if($blok->getId() == 120){
  	  if(!($event->isCancelled())){

		 //17
		$gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY(), $blok->getFloorZ()), new Obsidian());
  	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-1, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-1, $blok->getFloorZ()), new Obsidian());
	  }
	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-2, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-2, $blok->getFloorZ()), new Obsidian());
	  }
	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-3, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-3, $blok->getFloorZ()), new Obsidian());
	  }
	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-4, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-4, $blok->getFloorZ()), new Obsidian());
		  }
	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-5, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-5, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-6, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-6, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-7, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-7, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-8, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-8, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-9, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-9, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-10, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-10, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-11, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-11, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-12, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-12, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-13, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-13, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-14, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-14, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-15, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-15, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-16, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-16, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-17, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-17, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-18, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-18, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-19, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-19, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-20, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-20, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-21, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-21, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-22, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-22, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-23, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-23, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-24, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-24, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-25, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-25, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-26, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-26, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-27, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-27, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-28, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-28, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-29, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-29, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-30, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-30, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-31, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-31, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-32, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-32, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-33, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-33, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-34, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-34, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-35, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-35, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-36, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-36, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-37, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-37, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-38, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-38, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-39, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-39, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-40, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-40, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-41, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-41, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-42, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-42, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-43, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-43, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-44, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-44, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-45, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-45, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-46, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-46, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-47, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-47, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-48, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-48, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-49, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-49, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-50, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-50, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-51, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-51, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-52, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-52, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-53, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-53, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-54, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-54, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-55, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-55, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-56, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-56, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-57, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-57, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-58, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-58, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-59, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-59, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-60, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-60, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-61, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-61, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-62, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-62, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-63, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-63, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-64, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-64, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-65, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-65, $blok->getFloorZ()), new Obsidian());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-66, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-66, $blok->getFloorZ()), new Obsidian());
		  }
		  		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-67, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-67, $blok->getFloorZ()), new Obsidian());
		  }
		  		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-68, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-68, $blok->getFloorZ()), new Obsidian());
		  }
		  		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-69, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-69, $blok->getFloorZ()), new Obsidian());
		  }
		  		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-70, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-70, $blok->getFloorZ()), new Obsidian());
		  }
		  
     $gracz->sendMessage("§8• §7Postawiles §eBoyFarmera §8•");
        $center = new Vector3($x, $y, $z);
        $particle = new LavaParticle($center);
        for($yaw = 0, $y = $center->y; $y < $center->y + 3; $yaw += (M_PI * 2) / 20, $y += 1 / 20) {
            $x = -sin($yaw) + $center->x;
            $z = cos($yaw) + $center->z;
            $particle->setComponents($x, $y, $z);
    }
	  }else{
	   $gracz->sendMessage("");
	  }	 
	 }
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
 
 public function trueBojki(BlockPlaceEvent $event){
			$blok = $event->getBlock();
			$gracz = $event->getPlayer();
			  if($blok->getId() == 120){
				$event->setCancelled();
				$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
				 }
			}
			
	
	public function trueSandFarmer(BlockPlaceEvent $event){
	 $blok = $event->getBlock();
	 $gracz = $event->getPlayer();
	 $y = $blok->getFloorY();
	 $x = $blok->getFloorX();
	 $z = $blok->getFloorZ();
	 if ($this->plugin->isInPlot($event->getBlock()->getFloorX(), $event->getBlock()->getFloorZ())) {
            if ($this->plugin->inOwnPlot($event->getPlayer(), $event->getBlock()->getFloorX(), $event->getBlock()->getFloorZ())) {
                if($blok->getId() == 19){
  	  if(!($event->isCancelled())){

		 //17
		$gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY(), $blok->getFloorZ()), new Sand());
  	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-1, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-1, $blok->getFloorZ()), new Sand());
	  }
	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-2, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-2, $blok->getFloorZ()), new Sand());
	  }
	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-3, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-3, $blok->getFloorZ()), new Sand());
	  }
	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-4, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-4, $blok->getFloorZ()), new Sand());
		  }
	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-5, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-5, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-6, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-6, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-7, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-7, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-8, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-8, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-9, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-9, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-10, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-10, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-11, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-11, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-12, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-12, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-13, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-13, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-14, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-14, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-15, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-15, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-16, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-16, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-17, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-17, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-18, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-18, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-19, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-19, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-20, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-20, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-21, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-21, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-22, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-22, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-23, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-23, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-24, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-24, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-25, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-25, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-26, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-26, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-27, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-27, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-28, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-28, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-29, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-29, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-30, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-30, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-31, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-31, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-32, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-32, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-33, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-33, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-34, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-34, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-35, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-35, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-36, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-36, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-37, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-37, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-38, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-38, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-39, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-39, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-40, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-40, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-41, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-41, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-42, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-42, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-43, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-43, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-44, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-44, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-45, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-45, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-46, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-46, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-47, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-47, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-48, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-48, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-49, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-49, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-50, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-50, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-51, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-51, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-52, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-52, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-53, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-53, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-54, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-54, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-55, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-55, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-56, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-56, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-57, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-57, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-58, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-58, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-59, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-59, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-60, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-60, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-61, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-61, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-62, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-62, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-63, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-63, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-64, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-64, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-65, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-65, $blok->getFloorZ()), new Sand());
		  }
		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-66, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-66, $blok->getFloorZ()), new Sand());
		  }
		  		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-67, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-67, $blok->getFloorZ()), new Sand());
		  }
		  		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-68, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-68, $blok->getFloorZ()), new Sand());
		  }
		  		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-69, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-69, $blok->getFloorZ()), new Sand());
		  }
		  		  	    	   if(!($gracz->getLevel()->getBlock(new Vector3($x, $y-70, $z))->getId() == 7)) {
	   $gracz->getLevel()->setBlock(new Vector3($blok->getFloorX(), $blok->getFloorY()-70, $blok->getFloorZ()), new Sand());
		  }
		  
     $gracz->sendMessage("§8• §7Postawiles §eSandFarmera §8•");
        $center = new Vector3($x, $y, $z);
        $particle = new LavaParticle($center);
        for($yaw = 0, $y = $center->y; $y < $center->y + 3; $yaw += (M_PI * 2) / 20, $y += 1 / 20) {
            $x = -sin($yaw) + $center->x;
            $z = cos($yaw) + $center->z;
            $particle->setComponents($x, $y, $z);
    }
	  }else{
	   $gracz->sendMessage("");
	  }	 
	 }
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
 
 public function trueSandki(BlockPlaceEvent $event){
			$blok = $event->getBlock();
			$gracz = $event->getPlayer();
			  if($blok->getId() == 19){
				$event->setCancelled();
								$item = $event->getItem();
				$item->setCount($item->getCount() - 1);
				$gracz->getInventory()->setItemInHand($item);
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
    
   /*public function onProjectileHitEvent(ProjectileHitEvent $e) {
        $entity = $e->getEntity();
        $player = $entity->shootingEntity;    
		$x = floor($entity->getX());
		$y = floor($entity->getY());
		$z = floor($entity->getZ());
		$world = $player->getLevel()->getName();
        if($entity instanceof Arrow) {
            if($player instanceof Player) {
                if(!$this->plugin->isInPlot($x, $z)) {
                    return true;
                }                
                if($e->getEntity()->getLevel()->getBlock(new Vector3($x, $y, $z))->getId() == 49 || $e->getEntity()->getLevel()->getBlock(new Vector3($x + 1, $y, $z))->getId() == 49 || $e->getEntity()->getLevel()->getBlock(new Vector3($x - 1, $y, $z))->getId() == 49 || $e->getEntity()->getLevel()->getBlock(new Vector3($x, $y, $z + 1))->getId() == 49 || $e->getEntity()->getLevel()->getBlock(new Vector3($x, $y, $z - 1))->getId() == 49 || $e->getEntity()->getLevel()->getBlock(new Vector3($x, $y + 1, $z))->getId() == 49 || $e->getEntity()->getLevel()->getBlock(new Vector3($x, $y - 1, $z))->getId() == 49) {
                    if($this->plugin->prefs->get("ObsidianDestroyEnabled") == false) {
                        $player->sendMessage($this->plugin->formatMessage("Wysadzanie obsydianu jest wylaczone!"));
                        return true;
                    }
                    if(!$player->getInventory()->contains(Item::get(46, 0, 5)) || !$player->getInventory()->contains(Item::get(76, 0, 3)) || !$player->getInventory()->contains(Item::get(385, 0, 1))) {
                        return true;
                    }
                    
                    $time = explode("-", $this->plugin->prefs->get("NightExplosionProtection"));
                    if((date("H") >= $time[0]) && (date("H") <= $time[1])) {
                        $player->sendMessage($this->plugin->formatMessage("Nie mozesz wysadzac terenu w godzinach " . $time[0] . "-" . ($time[1] + 1) . "!"));
                        return true;
                    }                    
                    
                    $player->getInventory()->removeItem(Item::get(46, 0, 5));
                    $player->getInventory()->removeItem(Item::get(76, 0, 3));
                    $player->getInventory()->removeItem(Item::get(385, 0, 1));
                    
                    $chance = mt_rand(1, 100);
                    if($this->plugin->prefs->get("ObsidianDestroyChance") < $chance) {
                        return true;
                    }
                    $this->plugin->packetFakeExplode($world, $x, $y, $z, 2);
                    if($this->plugin->getServer()->getLevelByName($world)->getBlock(new Vector3($x, $y, $z))->getId() == 49) {
                        $this->plugin->getServer()->getLevelByName($world)->setBlock(new Vector3($x, $y, $z), Block::get(0, 0)); 
                    }
                    if($this->plugin->getServer()->getLevelByName($world)->getBlock(new Vector3($x + 1, $y, $z))->getId() == 49) {
                        $this->plugin->getServer()->getLevelByName($world)->setBlock(new Vector3($x + 1, $y, $z), Block::get(0, 0)); 
                    }  
                    if($this->plugin->getServer()->getLevelByName($world)->getBlock(new Vector3($x - 1, $y, $z))->getId() == 49) {
                        $this->plugin->getServer()->getLevelByName($world)->setBlock(new Vector3($x - 1, $y, $z), Block::get(0, 0)); 
                    } 
                    if($this->plugin->getServer()->getLevelByName($world)->getBlock(new Vector3($x, $y, $z + 1))->getId() == 49) {
                        $this->plugin->getServer()->getLevelByName($world)->setBlock(new Vector3($x, $y, $z + 1), Block::get(0, 0)); 
                    } 
                    if($this->plugin->getServer()->getLevelByName($world)->getBlock(new Vector3($x, $y, $z - 1))->getId() == 49) {
                        $this->plugin->getServer()->getLevelByName($world)->setBlock(new Vector3($x, $y, $z - 1), Block::get(0, 0)); 
                    }
                    if($this->plugin->getServer()->getLevelByName($world)->getBlock(new Vector3($x, $y + 1, $z))->getId() == 49) {
                        $this->plugin->getServer()->getLevelByName($world)->setBlock(new Vector3($x, $y + 1, $z), Block::get(0, 0)); 
                    } 
                    if($this->plugin->getServer()->getLevelByName($world)->getBlock(new Vector3($x, $y - 1, $z))->getId() == 49) {
                        $this->plugin->getServer()->getLevelByName($world)->setBlock(new Vector3($x, $y - 1, $z), Block::get(0, 0)); 
                    }                     
                }
            }        
        }
    }*/
    
    public function nightProtection(ExplosionPrimeEvent $e) {
        $time = explode("-", $this->plugin->prefs->get("NightExplosionProtection"));
        if((date("H") >= $time[0]) && (date("H") <= $time[1])) {
            $e->setCancelled();
        }
    }
		public function onDeathg(PlayerDeathEvent $event){
	if($this->plugin->isInFaction($event->getPlayer()->getName())){
	$gsmierc = $this->plugin->getPlayerFaction($event->getPlayer()->getName());
	$g2 = $this->plugin->smierci->get($gsmierc);
	$this->plugin->smierci->set($gsmierc, $g2+1);
	$this->plugin->smierci->save();
			}
		}
	public function onKillg(PlayerDeathEvent $event){
    $entity = $event->getEntity();
    $cause = $entity->getLastDamageCause();
    $killer = $cause->getDamager();
    if($killer instanceof Player){
	if($this->plugin->isInFaction($killer->getName())){
	$gsmierc = $this->plugin->getPlayerFaction($killer->getName());
	$g2 = $this->plugin->zabicia->get($gsmierc);
	$this->plugin->zabicia->set($gsmierc, $g2+1);
	$this->plugin->zabicia->save();
    }
  }
	}
    public function BlockStaty(PlayerCommandPreprocessEvent $event) {

      $command = explode(" ", strtolower($event->getMessage()));

      $player = $event->getPlayer();
	if($command[0] === "/staty") {
		$event->setCancelled();
		$player->sendMessage("§f• §8> §8[§2xHardCore§8] §7Uzyj: /gracz, aby zobaczyc swoje statystyki! §f•");
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
		if($this->plugin->isInPlot($x,$z)){
		if(!$this->plugin->inOwnPlot($player, $x, $z)){
		if(!$player->isOp() or !$player->hasPermission("intruz.admin")){
		$this->plugin->getServer()->getPlayer($row["player"])->sendPopup("§a" . $player->getName() . " §7na twoim terenie!");
	}
		}
		
	}
		}
}
	}
  public function FactionHelp(PlayerChatEvent $event){
	  $message = $event->getMessage();
	  $p = $event->getPlayer();
	  $faction = $this->plugin->getPlayerFaction($p->getName());
	  $array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
	  $x = floor($p->getX());
	  $y = floor($p->getY());
	  $z = floor($p->getZ());
	  while($row = $array->fetchArray(SQLITE3_ASSOC)) {  
	  if($this->plugin->isInFaction($p->getName())){
	  if($message == "!"){
	  if($this->plugin->getServer()->getPlayer($row["player"])) {
	  $event->setCancelled();
	  $this->plugin->getServer()->getPlayer($row["player"])->sendMessage("§f• §8> §8[§aGildie§8] §7Gracz§a " . $p->getName() . " §7potrzebuje pomocy! §7X: §a" . $x . " §7Y:§a " . $y . " §7Z: §a" . $z . " §f•");
	  }
	  }
  }
  }
}
}