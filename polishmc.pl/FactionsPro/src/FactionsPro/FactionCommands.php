<?php

namespace FactionsPro;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
class FactionCommands {
	
	public $plugin;
	
	public function __construct(FactionMain $pg) {
		$this->plugin = $pg;
	}
	
	public function onCommand(CommandSender $sender, Command $command, String $label, array $args) : bool{
		if($sender instanceof Player) {
			$player = $sender->getName();
			if(strtolower($command->getName('g'))) {
				if(empty($args)) {
					$sender->sendMessage($this->plugin->formatMessage("Prosze uzyc /g pomoc, aby wyswietlic liste komend."));
					return true;
				}
				if(count($args) > 0){
					
					/////////////////////////////// CREATE ///////////////////////////////
					
					if($args[0] == "create" || $args[0] == "zaloz") {
						if(!isset($args[1]) && !isset($args[2])) {
							$sender->sendMessage($this->plugin->formatMessage("Uzyj: /g zaloz <tag> <nazwa>"));
							return true;
						}
						if(isset($args[2])){
						if(!(strlen($args[2] <= 16))){
						$sender->sendMessage($this->plugin->formatMessage("Ta nazwa gidlii jest zbyt dluga maksymalna ilosc liter w nazwie to 16."));	
						}
						}
						if(isset($args[1]) && !isset($args[2])){
							$sender->sendMessage($this->plugin->formatMessage("Uzyj: /g zaloz $args[1] <nazwa>"));
							return true;	
						}
						if(!isset($args[1]) && isset($args[2])){
							$sender->sendMessage($this->plugin->formatMessage("Uzyj: /g zaloz <tag> $args[2]"));
							return true;
						}
						if(!(ctype_alnum($args[1]))) {
							$sender->sendMessage($this->plugin->formatMessage("Mozesz jedynie uzyc liter oraz cyfr!"));
							return true;
						}
						if($this->plugin->isNameBanned($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Ta nazwa nie jest dozwolona."));
							return true;
						}
						if($this->plugin->factionExists(strtolower($args[1])) == true) {
							$sender->sendMessage($this->plugin->formatMessage("Gildia o tej nazwie juz istnieje."));
							return true;
						}
						if(strlen($args[1]) > $this->plugin->prefs->get("MaxFactionNameLength")) {
							$sender->sendMessage($this->plugin->formatMessage("Ten tag jest zbyt dlugi! Maksymalna ilosc liter w tagu to 4"));
							return true;
						}
						if(isset($args[1]) && isset($args[2])){
							if($sender->getInventory()->contains(Item::get(264, 0, 64)) or $sender->hasPermission("gildie.darmowe")){
							if($sender->getInventory()->contains(Item::get(265, 0, 64)) or $sender->hasPermission("gildie.darmowe")){
							if($sender->getInventory()->contains(Item::get(266, 0, 64)) or $sender->hasPermission("gildie.darmowe")){
							if($sender->getInventory()->contains(Item::get(388, 0, 64)) or $sender->hasPermission("gildie.darmowe")){
							if($sender->getInventory()->contains(Item::get(332, 0, 16)) or $sender->hasPermission("gildie.darmowe")){
							if($sender->getInventory()->contains(Item::get(47, 0, 64)) or $sender->hasPermission("gildie.darmowe")){
							if($sender->getInventory()->contains(Item::get(46, 0, 64)) or $sender->hasPermission("gildie.darmowe")){
						if($this->plugin->isInFaction($sender->getName())) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz opuscic obecna gildie."));
							return true;                                              
						} else {
							$factionName = $args[1];                                                    

                                                        $x = floor($sender->getX());
                                                        $y = floor($sender->getY());
                                                        $z = floor($sender->getZ());
                                                        $level = $sender->getLevel(); 
														
                                                        foreach($this->plugin->prefs->get("BlackListWorlds") as $world) {
                                                            if(strtolower($sender->getLevel()->getName()) == $world) {
                                                                $sender->sendMessage($this->plugin->formatMessage("Nie mozesz zalozyc w tym swiecie gildii!"));
                                                                return true;
                                                            }
                                                        }
                                                        
                                                        if($sender->getPosition()->distance($sender->getLevel()->getSafeSpawn()) < $this->plugin->prefs->get("RegionMinDistanceFromSpawn")) {
                                                            $sender->sendMessage($this->plugin->formatMessage("Jestes zbyt blisko spawnu! Minimalna odleglosc to " . $this->plugin->prefs->get("RegionMinDistanceFromSpawn") . " kratek."));
                                                            return true;    
                                                        }
                                                        
                                                        $blockUnder = $level->getBlock(new Vector3($x, $y - 1, $z));
                                                        if($blockUnder->getId() == 7) {
                                                            $sender->sendMessage($this->plugin->formatMessage("Jestes zbyt blisko skaly macierzystej!"));
                                                            return true; 
                                                        }
                                                        
                                                        if($sender->getY() > 35) {
                                                            $sender->sendMessage($this->plugin->formatMessage("Jestes zbyt wysoko! Gildie mozesz zalozyc do Y: 35."));
                                                            return true;
                                                        }
                                                        
                                                        if(!$this->plugin->drawPlot($sender, $factionName, $x, $y, $z, $level, $this->plugin->prefs->get("PlotSize"))) {
                                                                return true;
                                                        }
                                                        
                                                        Server::getInstance()->broadcastMessage("§8• §7Gildia §8[§a" . $factionName . " §7-§a " . $args[2] . "§8] §7została załozona przez§a " . $sender->getName() . "§7! §8•");                                                                                                            
                                                                                                                
							$player = $player;                                                                   
							$rank = "Leader";
							$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank, chat, chat_type, chat_color) VALUES (:player, :faction, :rank, :chat, :chat_type, :chat_color);");
							$stmt->bindValue(":player", $player);
							$stmt->bindValue(":faction", $factionName);
							$stmt->bindValue(":rank", $rank);
                                                        $stmt->bindValue(":chat", 1);
                                                        $stmt->bindValue(":chat_type", "small");    
                                                        $stmt->bindValue(":chat_color", "&6");                                                                                                                
							$result = $stmt->execute();
							if($this->plugin->prefs->get("FactionNametags")) {
								
							}
                                                        $sender->getInventory()->removeItem(Item::get(264, 0, 64));
                                                        $sender->getInventory()->removeItem(Item::get(332, 0, 16));
                                                        $sender->getInventory()->removeItem(Item::get(288, 0, 64));
                                                        $sender->getInventory()->removeItem(Item::get(266, 0, 64));                        
                                                        $sender->getInventory()->removeItem(Item::get(265, 0, 64)); 
														$sender->getInventory()->removeItem(Item::get(46, 0, 64));
														$sender->getInventory()->removeItem(Item::get(120, 0, 12));
                                                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO center (faction, lives, timeWarProtection, timeWarWait, x, y, z, world) VALUES (:faction, :lives, :timeWarProtection, :timeWarWait, :x, :y, :z, :world);");
                                                        $stmt->bindValue(":faction", $factionName);
                                                        $stmt->bindValue(":lives", $this->plugin->prefs->get("WarLives"));
                                                        $stmt->bindValue(":timeWarProtection", time());
                                                        $stmt->bindValue(":timeWarWait", 0);                                                        
                                                        $stmt->bindValue(":x", $x);
                                                        $stmt->bindValue(":y", $y - 1);
                                                        $stmt->bindValue(":z", $z);
                                                        $stmt->bindValue(":world", $sender->getLevel()->getName());
                                                        $result = $stmt->execute();                                                         
                                                        
                                                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO top (faction, points, wins) VALUES (:faction, :points, :wins);");
                                                        $stmt->bindValue(":faction", $factionName);
                                                        $stmt->bindValue(":points", 1000);
                                                        $stmt->bindValue(":wins", 0);                                                        
                                                        $result = $stmt->execute();   
                                                        
                                                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO expires (faction, time) VALUES (:faction, :time);");
                                                        $stmt->bindValue(":faction", $factionName);
                                                        $stmt->bindValue(":time", $this->plugin->prefs->get("Expires"));
                                                        $result = $stmt->execute();
                                                        
                                                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO home (faction, x, y, z, world) VALUES (:faction, :x, :y, :z, :world);");
                                                        $stmt->bindValue(":faction", $factionName);
                                                        $stmt->bindValue(":x", $sender->getX());
                                                        $stmt->bindValue(":y", $sender->getY());
                                                        $stmt->bindValue(":z", $sender->getZ());
                                                        $stmt->bindValue(":world", $sender->getLevel()->getName());
                                                        $result = $stmt->execute();  
							$g = $this->plugin->getPlayerFaction($sender->getName());	
							$s = $this->plugin->prefs->get("PlotSize");
                            $this->plugin->pvp->set($g, "0");
							$this->plugin->zabicia->set($g, "0");
							$this->plugin->smierci->set($g, "0");
							$this->plugin->teren->set($g, $s);
							
							$this->plugin->zabicia->save();
							$this->plugin->smierci->save();
							$this->plugin->pvp->save(); 
							$this->plugin->teren->save();
							$faction = $args[1];
							$player = $sender->getName();
							$motd = $args[2];
                            $this->plugin->setMOTD($faction, $player, $motd);	
                            //Pustka
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()-1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()-1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()-1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()-1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY(), $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY(), $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY(), $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY(), $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY(), $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY(), $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY(), $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY(), $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY(), $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY(), $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY(), $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+2, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+2, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+2, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+2, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+2, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+2, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+2, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+2, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+2, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+2, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+2, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+2, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+2, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+2, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+2, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+2, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+2, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+2, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+3, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+3, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+3, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+3, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+3, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+3, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+3, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+3, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+3, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+3, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+3, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+3, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+3, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+3, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+3, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+3, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+3, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+3, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+4, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+4, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+4, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+4, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+4, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+4, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+4, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+4, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+4, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+4, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+4, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+4, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+4, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+4, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+4, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+4, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+4, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+4, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+5, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+5, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+5, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+5, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+5, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+5, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+5, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+5, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+5, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+5, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+5, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+5, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+5, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+5, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+5, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+5, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+5, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+5, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()-1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY(), $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+2, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+3, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+4, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+5, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()-1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY(), $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+2, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+3, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+4, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+5, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()-1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY(), $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+2, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+3, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+4, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+5, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()-1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY(), $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+2, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+3, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+4, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+5, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()-1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY(), $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+2, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+3, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+4, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+5, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()-1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY(), $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+2, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+3, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+4, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+5, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()-1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY(), $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+2, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+3, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+4, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+5, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()-1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY(), $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+2, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+3, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+4, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+5, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()-1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY(), $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+2, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+3, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+4, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+5, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()-1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY(), $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+2, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+3, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+4, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+5, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()-1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY(), $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+2, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+3, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+4, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+5, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()-1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY(), $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+2, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+3, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+4, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+5, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()-1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY(), $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+2, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+3, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+4, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+5, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()-1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY(), $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+2, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+3, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+4, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+5, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()-1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY(), $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+2, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+3, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+4, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+5, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()-1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY(), $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+2, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+3, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+4, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+5, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()-1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY(), $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+2, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+3, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+4, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+5, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()-1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY(), $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+2, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+3, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+4, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+5, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()-1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY(), $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+2, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+3, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+4, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+5, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()-1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY(), $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+1, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+2, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+3, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+4, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+5, $sender->getFloorZ()+3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()-1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY(), $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+2, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+3, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+4, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+5, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()-1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY(), $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+2, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+3, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+4, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+5, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()-1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY(), $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+2, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+3, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+4, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+5, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()-1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY(), $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+1, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+2, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+3, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+4, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+5, $sender->getFloorZ()-2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()-1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY(), $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+2, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+3, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+4, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+5, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()-1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY(), $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+2, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+3, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+4, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+5, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()-1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY(), $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+2, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+3, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+4, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+5, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()-1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY(), $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+1, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+2, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+3, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+4, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+5, $sender->getFloorZ()+1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()-1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY(), $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+1, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+2, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+3, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+4, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+5, $sender->getFloorZ()+2), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()-1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY(), $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+1, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+2, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+3, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+4, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+5, $sender->getFloorZ()-1), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()-1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY(), $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+2, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+3, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+4, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+5, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()-1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY(), $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+1, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+2, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+3, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+4, $sender->getFloorZ()-3), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+5, $sender->getFloorZ()-3), Block::get(0, 0));
							
							//Podstawa
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()-1, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()-1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()-2), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()-3), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()+1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()+2), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()-1, $sender->getFloorZ()+3), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()-1, $sender->getFloorZ()+1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()-1, $sender->getFloorZ()+1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()-1, $sender->getFloorZ()-1), Block::get(49, 0));
						    $sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()-1, $sender->getFloorZ()-1), Block::get(49, 0));
							
							//Kolumny
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+2, $sender->getFloorZ()-3), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+1, $sender->getFloorZ()-3), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ()-3), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+2, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY()+1, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-3, $sender->getFloorY(), $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+2, $sender->getFloorZ()+3), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+1, $sender->getFloorZ()+3), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ()+3), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+2, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY()+1, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+3, $sender->getFloorY(), $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+5, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+4, $sender->getFloorZ()-1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+4, $sender->getFloorZ()+1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+4, $sender->getFloorZ()+1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+4, $sender->getFloorZ()-1), Block::get(49, 0));
							
							//serce
                             $createMaterial = explode(":", $this->plugin->prefs->get("CreateMaterial"));
                            $level->setBlock(new Vector3($x, $y - 1, $z), Block::get($createMaterial[0], $createMaterial[1]));
							
							//Dach
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+3, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+2, $sender->getFloorY()+3, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+3, $sender->getFloorZ()+1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+3, $sender->getFloorZ()+2), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+3, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-2, $sender->getFloorY()+3, $sender->getFloorZ()), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+3, $sender->getFloorZ()-1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+3, $sender->getFloorZ()-2), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+3, $sender->getFloorZ()-1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()-1, $sender->getFloorY()+3, $sender->getFloorZ()+1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+3, $sender->getFloorZ()+1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX()+1, $sender->getFloorY()+3, $sender->getFloorZ()-1), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+4, $sender->getFloorZ()), Block::get(49, 0));     
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ()), Block::get(50, 0)); 							
                                        //
							$size = $this->plugin->prefs->get("PlotSize2");
							$arm = ($size - 1) / 2;
							$sender->getLevel()->setBlock(new Vector3($x + $arm, $y, $z + $arm), Block::get(49, 0));
							$sender->getLevel()->setBlock(new Vector3($x - $arm, $y, $z - $arm), Block::get(49, 0));
							
							 //pochodnia
                           /*$level->setBlock(new Vector3($x, $y, $z), Block::get(50, 0));       
                                                        */
                                               
							return true;
						}
							}
														else{
							$sender->sendMessage($this->plugin->formatMessage("Brakuje Ci 64 tnt!"));	
							}
							}
														else{
							$sender->sendMessage($this->plugin->formatMessage("Brakuje Ci 64 bookshelfow!"));	
							}
							}
														else{
							$sender->sendMessage($this->plugin->formatMessage("Brakuje Ci 16 perel!"));	
							}
							}
														else{
							$sender->sendMessage($this->plugin->formatMessage("Brakuje Ci 64 emeraldow!"));	
							}
							}
														else{
							$sender->sendMessage($this->plugin->formatMessage("Brakuje Ci 64 zlota!"));	
							}
							}
														else{
							$sender->sendMessage($this->plugin->formatMessage("Brakuje Ci 64 zelaza!"));	
							}
							}
							else{
							$sender->sendMessage($this->plugin->formatMessage("Brakuje Ci 64 diamenty!"));	
							}
						}
					}
					
					/////////////////////////////// INVITE ///////////////////////////////
					
					if($args[0] == "invite" || $args[0] == "zapros") {
						if(!isset($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Uzyj: /g zapros <gracz>"));
							return true;
						}
						if(!$this->plugin->isInFaction($player)) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
							return true;
						}
						if(!$this->plugin->isLeader($player) && !$this->plugin->hasPermission($player, "invite")) {
							$sender->sendMessage($this->plugin->formatMessage("Nie masz do tego uprawnien!"));
							return true;
						}
						if($this->plugin->isFactionFull(strtolower($this->plugin->getPlayerFaction($player)))) {
							$sender->sendMessage($this->plugin->formatMessage("Gildia jest pelna, nie ma miejsc na nowych czlonkow."));
							return true;
						}
						$invited = $this->plugin->getServer()->getPlayerExact($args[1]);
						if($this->plugin->isInFaction($invited) == true) {
							$sender->sendMessage($this->plugin->formatMessage("Gracz jest aktualnie w gildii."));
							return true;
						}
						if(!$invited instanceof Player) {
							$sender->sendMessage($this->plugin->formatMessage("Gracz nie jest online!"));
							return true;
						}
						$factionName = $this->plugin->getPlayerFaction($player);
						$invitedName = $invited->getName();
						$rank = "Member";
							
						$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO confirm (player, faction, invitedby, timestamp) VALUES (:player, :faction, :invitedby, :timestamp);");
						$stmt->bindValue(":player", strtolower($invitedName));
						$stmt->bindValue(":faction", $factionName);
						$stmt->bindValue(":invitedby", $sender->getName());
						$stmt->bindValue(":timestamp", time());
						$result = $stmt->execute();

						$sender->sendMessage($this->plugin->formatMessage("Gracz $invitedName zostal zaproszony!"));
						$invited->sendMessage($this->plugin->formatMessage("Zostales zaproszony do $factionName. Aby dolaczyc uzyj '/g dolacz' lub '/g odrzuc', aby odrzucic zaproszenie!"));
					}
					
					/////////////////////////////// LEADER ///////////////////////////////
					
					if($args[0] == "leader" || $args[0] == "lider") {
						if(!isset($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Uzyj: /g lider <gracz>"));
							return true;
						}
						if(!$this->plugin->isInFaction($sender->getName())) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
							return true;
						}
						if(!$this->plugin->isLeader($player)) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc liderem, aby uzyc tej opcji!"));
							return true;
						}
						if($this->plugin->getPlayerFaction($player) != $this->plugin->getPlayerFaction($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Tego gracza nie ma w gildii!"));
							return true;
						}		
						if(!$this->plugin->getServer()->getPlayerExact($args[1]) instanceof Player) {
							$sender->sendMessage($this->plugin->formatMessage("Gracz nie jest online!"));
							return true;
						}
							$factionName = $this->plugin->getPlayerFaction($player);
	
							$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank, chat, chat_type, chat_color) VALUES (:player, :faction, :rank, :chat, :chat_type, :chat_color);");
							$stmt->bindValue(":player", $player);
							$stmt->bindValue(":faction", $factionName);
							$stmt->bindValue(":rank", "Member");
                                                        $stmt->bindValue(":chat", 1);
                                                        $stmt->bindValue(":chat_type", "small");
                                                        $stmt->bindValue(":chat_color", "&6");
							$result = $stmt->execute();
	
							$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank, chat, chat_type, chat_color) VALUES (:player, :faction, :rank, :chat, :chat_type, :chat_color);");
							$stmt->bindValue(":player", $args[1]);
							$stmt->bindValue(":faction", $factionName);
							$stmt->bindValue(":rank", "Leader");
                                                   	$stmt->bindValue(":chat", 1);
                                                        $stmt->bindValue(":chat_type", "small");
                                                        $stmt->bindValue(":chat_color", "&6");
							$result = $stmt->execute();
	
	
							$sender->sendMessage($this->plugin->formatMessage("Nie jestes juz liderem!"));
							Server::getInstance()->broadcastMessage("§8• §7Gracz§a " . $args[1] . " §7został nowym liderem gildi§a " . $factionName . "§7! §8•");
							if($this->plugin->prefs->get("FactionNametags")) {

								
							}
						}
					
					/////////////////////////////// PROMOTE ///////////////////////////////
					
					if($args[0] == "promote" || $args[0] == "oficer") {
						if(!isset($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Uzyj: /g oficer <gracz>"));
							return true;
						}
						if(!$this->plugin->isInFaction($sender->getName())) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
							return true;
						}
						if(!$this->plugin->isLeader($player) && !$this->plugin->hasPermission($player, "promote")) {
							$sender->sendMessage($this->plugin->formatMessage("Nie masz do tego uprawnien!"));
							return true;
						}
						if($this->plugin->getPlayerFaction($player) != $this->plugin->getPlayerFaction($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Tego gracza nie ma w gildii!"));
							return true;
						}
						if($this->plugin->isOfficer($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Ten gracz jest aktualnie oficerem."));
							return true;
						}
						$factionName = $this->plugin->getPlayerFaction($player);
						$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank, chat, chat_type, chat_color) VALUES (:player, :faction, :rank, :chat, :chat_type, :chat_color);");
						$stmt->bindValue(":player", strtolower($args[1]));
						$stmt->bindValue(":faction", $factionName);
						$stmt->bindValue(":rank", "Officer");
						$stmt->bindValue(":chat", 1);
						$stmt->bindValue(":chat_type", "small");
						$stmt->bindValue(":chat_color", "&6");

						$result = $stmt->execute();
						$player = $args[1];
						$sender->sendMessage($this->plugin->formatMessage("" . "Gracz " . $player . " zostal awansowany na oficera!"));
						if($player = $this->plugin->getServer()->getPlayer($args[1])) {
							$player->sendMessage($this->plugin->formatMessage("Zostales awansowany na stopien oficera."));
						}
						if($this->plugin->prefs->get("FactionNametags")) {
								$this->plugin->updateTag($player->getName());
						}
					}
					
					/////////////////////////////// DEMOTE ///////////////////////////////
					
					if($args[0] == "demote" || $args[0] == "odbierz") {
						if(!isset($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Uzyj: /g odbierz <gracz>"));
							return true;
						}
						if($this->plugin->isInFaction($sender->getName()) == false) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
							return true;
						}
						if(!$this->plugin->isLeader($player) && !$this->plugin->hasPermission($player, "demote")) {
							$sender->sendMessage($this->plugin->formatMessage("Nie masz do tego uprawnien!"));
							return true;
						}
						if($this->plugin->getPlayerFaction($player) != $this->plugin->getPlayerFaction($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Tego gracza nie ma w gildii!"));
							return true;
						}
						if(!$this->plugin->isOfficer($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Ten gracz jest aktualnie czlonkiem."));
							return true;
						}
						$factionName = $this->plugin->getPlayerFaction($player);
						$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank) VALUES (:player, :faction, :rank);");
						$stmt->bindValue(":player", strtolower($args[1]));
						$stmt->bindValue(":faction", $factionName);
						$stmt->bindValue(":rank", "Member");
						$result = $stmt->execute();
						$player = $args[1];
						$sender->sendMessage($this->plugin->formatMessage("" . "Gracz " . $player . " zostal zniesiony na stopien czlonka!"));
						
						if($player = $this->plugin->getServer()->getPlayer($args[1])) {
							$player->sendMessage($this->plugin->formatMessage("Zostales zniesiony na stopien czlonka."));
						}
						if($this->plugin->prefs->get("FactionNametags")) {
							$this->plugin->updateTag($player->getName());
						}
					}
					
					/////////////////////////////// KICK ///////////////////////////////
					
					if($args[0] == "kick" || $args[0] == "wyrzuc") {
						if(!isset($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Uzyj: /g wyrzuc <gracz>"));
							return true;
						}
						if($this->plugin->isInFaction($sender->getName()) == false) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
							return true;
						}
						if(!$this->plugin->isLeader($player) && !$this->plugin->hasPermission($player, "kick")) {
							$sender->sendMessage($this->plugin->formatMessage("Nie masz do tego uprawnien!"));
							return true;
						}
						if($this->plugin->getPlayerFaction($player) != $this->plugin->getPlayerFaction($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Tego gracza nie ma w gildii!"));
							return true;
						}
                                                if($this->plugin->isLeader($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("Nie mozesz wyrzucic lidera gildii!"));
							return true;                                                    
                                                }
						$kicked = $this->plugin->getServer()->getPlayer($args[1]);
						$factionName = $this->plugin->getPlayerFaction($player);
						Server::getInstance()->broadcastMessage("§8• §7Gracz§a " . $args[1] . " §7został wyrzucony z§a " . $factionName . "§7! §8•");
						$this->plugin->db->query("DELETE FROM master WHERE player='$args[1]';");
						$players[] = $this->plugin->getServer()->getOnlinePlayers();
						if(in_array($args[1], $players) == true) {
							if($this->plugin->prefs->get("FactionNametags")) {
								$this->plugin->updateTag($args[1]);
							}
							return true;
						}
					}
					
					/////////////////////////////// INFO ///////////////////////////////
					
					if(strtolower($args[0] == "info" || $args[0] == "informacje")) {
						if(isset($args[1])) {
							if( !(ctype_alnum($args[1])) | !($this->plugin->factionExists(strtolower($args[1])))) {
								$sender->sendMessage($this->plugin->formatMessage("Ta gildia nie istnieje!"));
								return true;
							}
							$faction = strtolower($args[1]);
							$result = $this->plugin->db->query("SELECT * FROM motd WHERE LOWER(faction)='$faction';");
							$array = $result->fetchArray(SQLITE3_ASSOC);
							$message = $array["message"];
							$leader = $this->plugin->getLeader($faction);
							$numPlayers = $this->plugin->getNumberOfPlayers($faction);
							$gildies = $this->plugin->zabicia->get($args[1]);
							$gildies2 = $this->plugin->smierci->get($args[1]);
							if($gildies == 0){
								$gildies3 = 0;
							}else{
							$gildies3 = round($gildies / $gildies2, 2);
							}
							$teren = $this->plugin->teren->get($args[1]);
							$pvpdata = $this->plugin->pvp->get($args[1]);
							$maxplayers = $this->plugin->prefs->get("MaxPlayersPerFaction");
							if($pvpdata >= 1){
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Tag: " . TextFormat::YELLOW . strtoupper($faction));
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Nazwa: " . TextFormat::YELLOW . "$message");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Lider: " . TextFormat::YELLOW . "$leader");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Czlonkow: " . TextFormat::YELLOW . "" . $numPlayers . "§7/§3" . $maxplayers);
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Punkty: " . TextFormat::YELLOW . $this->plugin->getFactionPoints($faction)); 
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Zycia: " . TextFormat::YELLOW . $this->plugin->getFactionWarLives($faction));                                                          
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Podbite Gildie: " . TextFormat::YELLOW . $this->plugin->getFactionWins($faction));
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Zabojstwa: " . TextFormat::YELLOW . $gildies);  
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Smierci: " . TextFormat::YELLOW . $gildies2);
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "K/D: " . TextFormat::YELLOW . $gildies3);    
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "PVP: " . TextFormat::YELLOW . "ON");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Teren: " . TextFormat::YELLOW . $teren . "x" . $teren);      							
                            $time = $this->plugin->db->query("SELECT * FROM expires WHERE LOWER(faction)='$faction';");
                            $array = $time->fetchArray(SQLITE3_ASSOC);
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Wygasa za: " . TextFormat::YELLOW . floor($array["time"] / 1440) . TextFormat::GRAY . " dni, " . TextFormat::YELLOW . floor((($array["time"] / 1440) * 24) % 24) . TextFormat::GRAY . " godzin i " . TextFormat::YELLOW . floor(((($array["time"] / 1440) * 24) * 60) % 60) . TextFormat::GRAY . " minut");                                                          
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							}
						else{
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Tag: " . TextFormat::YELLOW . strtoupper($faction));
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Nazwa: " . TextFormat::YELLOW . "$message");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Lider: " . TextFormat::YELLOW . "$leader");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Czlonkow: " . TextFormat::YELLOW . "" . $numPlayers . "§7/§3" . $maxplayers);
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Punkty: " . TextFormat::YELLOW . $this->plugin->getFactionPoints($faction)); 
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Zycia: " . TextFormat::YELLOW . $this->plugin->getFactionWarLives($faction));                                                          
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Podbite Gildie: " . TextFormat::YELLOW . $this->plugin->getFactionWins($faction));
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Zabojstwa: " . TextFormat::YELLOW . $gildies);  
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Smierci: " . TextFormat::YELLOW . $gildies2);
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "K/D: " . TextFormat::YELLOW . $gildies3);    
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "PVP: " . TextFormat::YELLOW . "OFF");   
                            $sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Teren: " . TextFormat::YELLOW . $teren . "x" . $teren);
							$time = $this->plugin->db->query("SELECT * FROM expires WHERE LOWER(faction)='$faction';");
                            $array = $time->fetchArray(SQLITE3_ASSOC);
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Wygasa za: " . TextFormat::YELLOW . floor($array["time"] / 1440) . TextFormat::GRAY . " dni, " . TextFormat::YELLOW . floor((($array["time"] / 1440) * 24) % 24) . TextFormat::GRAY . " godzin i " . TextFormat::YELLOW . floor(((($array["time"] / 1440) * 24) * 60) % 60) . TextFormat::GRAY . " minut");                                                          
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");	
						}
						}
						else {
                           if($this->plugin->isInFaction($player)) {                                                    
                           $faction = $this->plugin->getPlayerFaction(strtolower($sender->getName()));
                           $faction = strtolower($faction);
						   $sfaction = $this->plugin->getPlayerFaction($sender->getName());
                           $result = $this->plugin->db->query("SELECT * FROM motd WHERE LOWER(faction)='$faction';");
                           $array = $result->fetchArray(SQLITE3_ASSOC);
                           $message = $array["message"];
                           $leader = $this->plugin->getLeader($faction);
                           $numPlayers = $this->plugin->getNumberOfPlayers($faction);
						   	$gildies = $this->plugin->zabicia->get($sfaction);
							$gildies2 = $this->plugin->smierci->get($sfaction);
							if($gildies == 0){
								$gildies3 = 0;
							}else{
							$gildies3 = round($gildies / $gildies2, 2);
							}
							$teren = $this->plugin->teren->get($sfaction);
							$pvpdata = $this->plugin->pvp->get($sfaction);
							$maxplayers = $this->plugin->prefs->get("MaxPlayersPerFaction");
							if($pvpdata >= 1){
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Tag: " . TextFormat::YELLOW . strtoupper($faction));
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Nazwa: " . TextFormat::YELLOW . "$message");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Lider: " . TextFormat::YELLOW . "$leader");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Czlonkow: " . TextFormat::YELLOW . "" . $numPlayers . "§7/§3" . $maxplayers);
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Punkty: " . TextFormat::YELLOW . $this->plugin->getFactionPoints($faction)); 
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Zycia: " . TextFormat::YELLOW . $this->plugin->getFactionWarLives($faction));                                                          
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Podbite Gildie: " . TextFormat::YELLOW . $this->plugin->getFactionWins($faction));
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Zabojstwa: " . TextFormat::YELLOW . $gildies);  
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Smierci: " . TextFormat::YELLOW . $gildies2);
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "K/D: " . TextFormat::YELLOW . $gildies3);    
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "PVP: " . TextFormat::YELLOW . "ON");    
                            $sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Teren: " . TextFormat::YELLOW . $teren . "x" . $teren); 
							$time = $this->plugin->db->query("SELECT * FROM expires WHERE LOWER(faction)='$faction';");
                            $array = $time->fetchArray(SQLITE3_ASSOC);
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Wygasa za: " . TextFormat::YELLOW . floor($array["time"] / 1440) . TextFormat::GRAY . " dni, " . TextFormat::YELLOW . floor((($array["time"] / 1440) * 24) % 24) . TextFormat::GRAY . " godzin i " . TextFormat::YELLOW . floor(((($array["time"] / 1440) * 24) * 60) % 60) . TextFormat::GRAY . " minut");                                                          
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							}
							else{
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Tag: " . TextFormat::YELLOW . strtoupper($faction));
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Nazwa: " . TextFormat::YELLOW . "$message");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Lider: " . TextFormat::YELLOW . "$leader");
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Czlonkow: " . TextFormat::YELLOW . "" . $numPlayers . "§7/§3" . $maxplayers);
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Punkty: " . TextFormat::YELLOW . $this->plugin->getFactionPoints($faction)); 
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Zycia: " . TextFormat::YELLOW . $this->plugin->getFactionWarLives($faction));                                                          
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Podbite Gildie: " . TextFormat::YELLOW . $this->plugin->getFactionWins($faction));
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Zabojstwa: " . TextFormat::YELLOW . $gildies);  
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Smierci: " . TextFormat::YELLOW . $gildies2);
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "K/D: " . TextFormat::YELLOW . $gildies3);    
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "PVP: " . TextFormat::YELLOW . "OFF");   
                            $sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Teren: " . TextFormat::YELLOW . $teren . "x" . $teren);
							$time = $this->plugin->db->query("SELECT * FROM expires WHERE LOWER(faction)='$faction';");
                            $array = $time->fetchArray(SQLITE3_ASSOC);
							$sender->sendMessage(TextFormat::YELLOW . "* " . TextFormat::GRAY . "Wygasa za: " . TextFormat::YELLOW . floor($array["time"] / 1440) . TextFormat::GRAY . " dni, " . TextFormat::YELLOW . floor((($array["time"] / 1440) * 24) % 24) . TextFormat::GRAY . " godzin i " . TextFormat::YELLOW . floor(((($array["time"] / 1440) * 24) * 60) % 60) . TextFormat::GRAY . " minut");                                                          
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");		
							}
						   } else {
                           $sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
                           return true;
                               }
						}
					}
					if(strtolower($args[0] == "help" || $args[0] == "pomoc")) {
						if(!isset($args[1]) || $args[1] == 1) {
           						$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
                                                        
							$sender->sendMessage(str_replace('&', '§', "&cStrona 1 z 3\n&a* &6/g plugin &7- informacje o pluginie.\n&a* &6/g dolacz &7- dolacza do gildii.\n&a* &6/g teren &7- zabezpiecza teren gildii.\n&a* &6/g zaloz <nazwa gildii> &7- zaklada gildie.\n&a* &6/g usun &7- usuwa gildie.\n&a* &6/g odbierz <gracz> &7- odbiera oficera.\n&a* &6/g odrzuc &7- odrzuca zaproszenie."));
                                                        
                                                        $sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							return true;
						}
						if($args[1] == 2) {
           						$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
                                                    
							$sender->sendMessage(str_replace('&', '§', "&cStrona 2 z 3\n&a* &6/g dom &7- teleport do domu gildii.\n&a* &6/g pomoc <strona> &7- lista komend.\n&a* &6/g info &7- informacje o gildii.\n&a* &6/g info <nazwa gildii> &7- informacje o gildii.\n&a* &6/g zapros <gracz> &7- zaprasza do gildii.\n&a* &6/g wyrzuc <gracz> &7- wyrzuca z gildii.\n&a* &6/g lider <gracz> &7- oddaje wlasciciela gildii.\n&a* &6/g opusc &7- opuszcza gildie."));
           						
                                                        $sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							
                                                        return true;
						} else {
           						$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
                                                    
							$sender->sendMessage(str_replace('&', '§', "&cStrona 3 z 3\n&a* &6/g motto &7- ustawia motto gildii.\n&a* &6/g oficer <gracz> &7- przydziela oficera gildii.\n&a* &6/g ustawdom &7- ustawia dom gildii.\n&a* &6/g usunteren &7- odbezpiecza teren gildii.\n&a* &6/g usundom &7- usuwa dom gildii.\n&a* &6/g ranking &7- ranking gildii.\n&a* &6/g czat &7- wlacza/wylacza czat.\n&a* &6/g typ &7- powieksza/pomniejsza czat.\n&a* &6/g kolor <0-9, a, b, c, d, e, f> &7- zmienia kolor czatu.\n&a* &6/g przedluz &7- przedluza waznosc gildii.\n&a* &6/g lista &7- lista czlonkow.\n&a* &6/g itemy &7- lista potrzebnych przedmiotow.\n&a* &6/g efekty &7- wysyla liste efektow.\n&a* &6/g koszt &7- wysyla koszt efektow.\n&a* &6/g efekt <numer> &7- dodaje efekt calej gildii."));
							
           						$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
                                                        
                                                        return true;
						}
					}
				}
				if(count($args) > 0) {
					
					/////////////////////////////// CLAIM ///////////////////////////////
					
					/*if(strtolower($args[0] == "claim" || $args[0] == "teren")) {
						if($this->plugin->prefs->get("ClaimingEnabled") == false) {
							$sender->sendMessage($this->plugin->formatMessage("Zabezpieczanie terenu gildii jest wylaczone!"));
							return true;
						}
						if(!$this->plugin->isInFaction($player)) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
							return true;
						}
						if(!$this->plugin->isLeader($player) && !$this->plugin->hasPermission($player, "claim")) {
							$sender->sendMessage($this->plugin->formatMessage("Nie masz do tego uprawnien!"));
							return true;
						}
						if($this->plugin->inOwnPlot($sender, $sender->getFloorX(), $sender->getFloorZ())) {
							$sender->sendMessage($this->plugin->formatMessage("Ten teren jest juz zajety!"));
							return true;
						}
						$x = floor($sender->getX());
						$y = floor($sender->getY());
						$z = floor($sender->getZ());
						$faction = $this->plugin->getPlayerFaction($sender->getPlayer()->getName());
						if(!$this->plugin->drawPlot($sender, $faction, $x, $y, $z, $sender->getPlayer()->getLevel(), $this->plugin->prefs->get("PlotSize"))) {
							return true;
                                                }
						$sender->sendMessage($this->plugin->formatMessage("Teren gildii zostal zabezpieczony."));
                                        }
					
					/////////////////////////////// UNCLAIM ///////////////////////////////
					
					if(strtolower($args[0] == "unclaim" || $args[0] == "usunteren")) {
						if($this->plugin->prefs->get("ClaimingEnabled") == false) {
							$sender->sendMessage($this->plugin->formatMessage("Odbezpieczanie terenu gildii jest wylaczone!"));
							return true;
						}
						if(!$this->plugin->isLeader($player) && !$this->plugin->hasPermission($player, "unclaim")) {
							$sender->sendMessage($this->plugin->formatMessage("Nie masz do tego uprawnien!"));
							return true;
						}
						$faction = $this->plugin->getPlayerFaction($sender->getName());
						$this->plugin->db->query("DELETE FROM plots WHERE faction='$faction';");
						$sender->sendMessage($this->plugin->formatMessage("Teren gildii zostal odbezpieczony."));
					}*/
					
					/////////////////////////////// MOTD ///////////////////////////////
					
					/*if(strtolower($args[0] == "motd" || $args[0] == "motto")) {
						if($this->plugin->isInFaction($sender->getName()) == false) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
							return true;
						}
						if(!$this->plugin->isLeader($player) && !$this->plugin->hasPermission($player, "motd")) {
							$sender->sendMessage($this->plugin->formatMessage("Nie masz do tego uprawnien!"));
							return true;
						}
						$sender->sendMessage($this->plugin->formatMessage("Wprowadz motto gildii na czacie."));
						$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO motdrcv (player, timestamp) VALUES (:player, :timestamp);");
						$stmt->bindValue(":player", strtolower($sender->getName()));
						$stmt->bindValue(":timestamp", time());
						$result = $stmt->execute();
					}*/
					/////////////////////////////// ACCEPT ///////////////////////////////
					
					if(strtolower($args[0] == "accept" || $args[0] == "akceptuj" || $args[0] == "dolacz")) {
						$player = $sender->getName();
						$lowercaseName = strtolower($player);
						$result = $this->plugin->db->query("SELECT * FROM confirm WHERE player='$lowercaseName';");
						$array = $result->fetchArray(SQLITE3_ASSOC);
						if(empty($array) == true) {
							$sender->sendMessage($this->plugin->formatMessage("Nie zostales zaproszony do zadnej gildii!"));
							return true;
						}
						$invitedTime = $array["timestamp"];
						$currentTime = time();
						if(($currentTime - $invitedTime) <= 60) { //This should be configurable
						if($sender->getInventory()->contains(Item::get(264, 0, 10))){
							$faction = $array["faction"];
							$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO master (player, faction, rank, chat, chat_type, chat_color) VALUES (:player, :faction, :rank, :chat, :chat_type, :chat_color);");
							$stmt->bindValue(":player", strtolower($player));
							$stmt->bindValue(":faction", $faction);
							$stmt->bindValue(":rank", "Member");
                                                        $stmt->bindValue(":chat", 1);
                                                        $stmt->bindValue(":chat_type", "small");  
                                                        $stmt->bindValue(":chat_color", "&6");                                                                                                                   
							$result = $stmt->execute();
							$this->plugin->db->query("DELETE FROM confirm WHERE player='$lowercaseName';");
							if($this->plugin->getServer()->getPlayer($array["invitedby"])) {
								if($this->plugin->getServer()->getPlayer($array["invitedby"])) {
									Server::getInstance()->broadcastMessage("§8• §7Gracz§3 " . $sender->getName() . " §7dołaczył do gildi§3 " . $faction . "§7! §8•");
								}
							}
							if($this->plugin->prefs->get("FactionNametags")) {

							}
						} else {
							$sender->sendMessage($this->plugin->formatMessage("Aby dołączyć do gildii musisz mieć 10 diamentów!!"));
						}
					} else {
							$sender->sendMessage($this->plugin->formatMessage("Zaproszenie juz wygaslo!"));
							$this->plugin->db->query("DELETE FROM confirm WHERE player='$lowercaseName';");
					}
					}
					/////////////////////////////// DENY ///////////////////////////////
					
					if(strtolower($args[0] == "deny" || $args[0] == "odrzuc")) {
						$player = $sender->getName();
						$lowercaseName = strtolower($player);
						$result = $this->plugin->db->query("SELECT * FROM confirm WHERE player='$lowercaseName';");
						$array = $result->fetchArray(SQLITE3_ASSOC);
						if(empty($array) == true) {
							$sender->sendMessage($this->plugin->formatMessage("Nie zostales zaproszony do gildii!"));
							return true;
						}
						$invitedTime = $array["timestamp"];
						$currentTime = time();
						if( ($currentTime - $invitedTime) <= 60 ) { //This should be configurable
							$this->plugin->db->query("DELETE FROM confirm WHERE player='$lowercaseName';");
							$sender->sendMessage($this->plugin->formatMessage("Odrzucono zaproszenie!"));
							$this->plugin->getServer()->getPlayerExact($array["invitedby"])->sendMessage($this->plugin->formatMessage("Gracz $player odrzucil zaproszenie!"));
						} else {
							$sender->sendMessage($this->plugin->formatMessage("Zaproszenie juz wygaslo!"));
							$this->plugin->db->query("DELETE FROM confirm WHERE player='$lowercaseName';");
						}
					}
					
					/////////////////////////////// DELETE ///////////////////////////////
					
					if(strtolower($args[0] == "del" || $args[0] == "usun")) {
						if($this->plugin->isInFaction($player) == true) {
							if($this->plugin->isLeader($player)) {
								Server::getInstance()->broadcastMessage("§8• §7Gildia§3 " . $this->plugin->getPlayerFaction($sender->getName()) . " §7została rozwiązana przez§3 " . $sender->getName() . "§7! §8•");
                                                                $factionDeleteClaim = $this->plugin->getPlayerFaction($sender->getName());
                                                                $this->plugin->db->query("DELETE FROM plots WHERE faction='$factionDeleteClaim';");                                                            
								$this->plugin->db->query("DELETE FROM master WHERE faction='$factionDeleteClaim';");
                                                                $this->plugin->db->query("DELETE FROM top WHERE faction='$factionDeleteClaim';");
                                                                $this->plugin->db->query("DELETE FROM expires WHERE faction='$factionDeleteClaim';");
                                                                $this->plugin->db->query("DELETE FROM home WHERE faction='$factionDeleteClaim';");
                                                                $result = $this->plugin->db->query("SELECT * FROM center WHERE faction='$factionDeleteClaim';");
                                                                $array = $result->fetchArray(SQLITE3_ASSOC);
                                                                $x = $array["x"];
                                                                $y = $array["y"];
                                                                $z = $array["z"];
                                                                $world = $array["world"];
                                                                $this->plugin->getServer()->getLevelByName($world)->setBlock(new Vector3($array["x"], $array["y"], $array["z"]), Block::get(0));                                                                   
                                                                $this->plugin->db->query("DELETE FROM center WHERE faction='$factionDeleteClaim';");                                                                                
								if($this->plugin->prefs->get("FactionNametags")) {
	
								}
							} else {
								$sender->sendMessage($this->plugin->formatMessage("Nie jestes liderem gildii!"));
							}
						} else {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
						}
					}
					
					/////////////////////////////// LEAVE ///////////////////////////////
					
 					if(strtolower($args[0] == "opusc") or strtolower($args[0] == "leave")) {
						if($this->plugin->isLeader($sender->getName()) == false) {
							$remove = $sender->getPlayer()->getNameTag();
							$faction = $this->plugin->getPlayerFaction($player);
							$name = $sender->getName();
							if($this->plugin->isInFaction($sender->getName())) {
							Server::getInstance()->broadcastMessage("§8• §7Gracz§a " . $sender->getName() . " §7opuścil gildie§a " . $faction ."§7! §8•");
						    $this->plugin->db->query("DELETE FROM master WHERE player='$name';");
						} else {
							$sender->sendMessage($this->plugin->formatMessage("Niemasz gildii!"));
						}
					} else {
							$sender->sendMessage($this->plugin->formatMessage("Jestes liderem, nie mozesz opuscic gildii!"));
						}
					}
					
					/////////////////////////////// SETHOME ///////////////////////////////
					
					if(strtolower($args[0] == "sethome" || $args[0] == "ustawdom")) {
						if(!$this->plugin->isInFaction($player)) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
							return true;
						}
						if(!$this->plugin->isLeader($player) && !$this->plugin->hasPermission($player, "sethome")) {
							$sender->sendMessage($this->plugin->formatMessage("Nie masz do tego uprawnien!"));
							return true;
						}
                                                if(!$this->plugin->inOwnPlot($sender, $sender->getX(), $sender->getZ())) {
                                                        $sender->sendMessage($this->plugin->formatMessage("Nie mozesz ustawic domu gildii poza swoim terenem!"));
                                                        return true;
                                                }
						$factionName = $this->plugin->getPlayerFaction($sender->getName());
						$stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO home (faction, x, y, z, world) VALUES (:faction, :x, :y, :z, :world);");
						$stmt->bindValue(":faction", $factionName);
						$stmt->bindValue(":x", $sender->getX());
						$stmt->bindValue(":y", $sender->getY());
						$stmt->bindValue(":z", $sender->getZ());
						$stmt->bindValue(":world", $sender->getLevel()->getName());
						$result = $stmt->execute();
						$sender->sendMessage($this->plugin->formatMessage("Dom gildii zostal ustawiony!"));
					}
					
					/////////////////////////////// UNSETHOME ///////////////////////////////
						
					if(strtolower($args[0] == "unsethome" || $args[0] == "usundom")) {
						if(!$this->plugin->isInFaction($player)) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
							return true;
						}
						if(!$this->plugin->isLeader($player) && !$this->plugin->hasPermission($player, "unsethome")) {
							$sender->sendMessage($this->plugin->formatMessage("Nie masz do tego uprawnien!"));
							return true;
						}
						$faction = $this->plugin->getPlayerFaction($sender->getName());
						$this->plugin->db->query("DELETE FROM home WHERE faction = '$faction';");
						$sender->sendMessage($this->plugin->formatMessage("Dom gildii zostal usuniety!"));
					}
					
					/////////////////////////////// HOME ///////////////////////////////
						
					if(strtolower($args[0] == "home" || $args[0] == "dom")) {
						if(!$this->plugin->isInFaction($player)) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
						}
						if(!$this->plugin->isLeader($player) && !$this->plugin->hasPermission($player, "home")) {
							$sender->sendMessage($this->plugin->formatMessage("Nie masz do tego uprawnien!"));
							return true;
						}
						$faction = $this->plugin->getPlayerFaction($sender->getName());
						$result = $this->plugin->db->query("SELECT * FROM home WHERE faction = '$faction';");
						$array = $result->fetchArray(SQLITE3_ASSOC);
						if(!empty($array)) {
							$world = $this->plugin->getServer()->getLevelByName($array['world']);
+							$sender->getPlayer()->teleport(new Position($array['x'], $array['y'], $array['z'], $world));
							$sender->sendMessage($this->plugin->formatMessage("Teleportowanie do domu gildii."));
							return true;
						} else {
							$sender->sendMessage($this->plugin->formatMessage("Dom gildii nie jest ustawiony."));
							}
						}
					
					/////////////////////////////// ABOUT ///////////////////////////////
					
                                        if(strtolower($args[0] == "top" || $args[0] == "ranking")) {
                                            $this->plugin->getFactionTop();
                                            $sender->sendMessage(str_replace('&', '§', "&8[ &7=========== &8[ &e&lRANKING GILDII&r &8] &7=========== &8]"));                
                                            for($i=1; $i<11; $i++) {
                                                if(!empty($this->plugin->getTop[$i])) {
                                                    $sender->sendMessage(str_replace('&', '§', "&e$i. " . $this->plugin->getTop[$i]));
                                                } else {
                                                    $sender->sendMessage(str_replace('&', '§', "&e$i. &7BRAK"));                    
                                                }
                                            }
                                            $sender->sendMessage(str_replace('&', '§', "&8[ &7=========== &8[ &e&lRANKING GILDII&r &8] &7=========== &8]"));                 
                                            unset($this->plugin->getTop);
                                            return true; 
                                        }
                                        if(strtolower($args[0] == "chat" || $args[0] == "czat")) {
                                            if($this->plugin->isInFaction($player)) {
                                                if($this->plugin->getFactionChat($player) == 1) {
                                                    $this->plugin->db->query("UPDATE master SET chat = '0' WHERE player='$player';");
                                                    $sender->sendMessage($this->plugin->formatMessage("Opusciles czat gildyjny. "));
                                                    return true;
                                                } else {
                                                    $this->plugin->db->query("UPDATE master SET chat = '1' WHERE player='$player';");
                                                    $sender->sendMessage($this->plugin->formatMessage("Dolaczyles do czatu gildyjnego. "));
                                                    return true;                                                    
                                                }
                                            } else {
                                                $sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
                                                return true;
                                            }
                                        }
                                        if(strtolower($args[0] == "type" || $args[0] == "typ")) {
                                            if($this->plugin->isInFaction($player)) {
                                                if($this->plugin->getFactionChatType($player) == "large") {
                                                    $this->plugin->db->query("UPDATE master SET chat_type = 'small' WHERE player='$player';");
                                                    $sender->sendMessage($this->plugin->formatMessage("Czat gildyjny zostal pomniejszony."));
                                                    return true;
                                                } else {
                                                    $this->plugin->db->query("UPDATE master SET chat_type = 'large' WHERE player='$player';");
                                                    $sender->sendMessage($this->plugin->formatMessage("Czat gildyjny zostal powiekszony."));
                                                    return true;                                                    
                                                }
                                            } else {
                                                $sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
                                                return true;
                                            }
                                        }
                                        if(strtolower($args[0] == "color" || $args[0] == "kolor")) {
                                            if(count($args) == 2) {
                                                if($args[1] == "1" || $args[1] == "2" || $args[1] == "3" || $args[1] == "4" || $args[1] == "5" || $args[1] == "6" || $args[1] == "7" || $args[1] == "8" || $args[1] == "9" || $args[1] == "0" || $args[1] == "a" ||$args[1] == "e" || $args[1] == "d" || $args[1] == "c" || $args[1] == "b" || $args[1] == "f") {                                     
                                                    if($this->plugin->isInFaction($player)) {
                                                        $this->plugin->db->query("UPDATE master SET chat_color = '&$args[1]' WHERE player='$player';");
                                                        $sender->sendMessage($this->plugin->formatMessage(str_replace('&', '§', "Kolor czatu gildyjnego zostal zmieniony na &$args[1]Kolor&7."), true));
                                                        return true;                                                
                                                    } else {
                                                        $sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
                                                        return true;
                                                    }
                                                } else {
                                                    $sender->sendMessage($this->plugin->formatMessage("Blad! Mozesz wybrac jedynie te kolory <0-9, a, b, c, d, e, f>."));
                                                    return true;                                                    
                                                }
                                            } else {
                                                $sender->sendMessage($this->plugin->formatMessage("Blad! Poprawne uzycie to /g kolor <0-9, a, b, c, d, e, f>."));
                                                return true;
                                            }
                                        }
                                        if(strtolower($args[0]) == "payment" || $args[0] == "oplata" || $args[0] == "oplac" || $args[0] == "waznosc" || $args[0] == "przedluz") {
                                            if(!$this->plugin->isInFaction($player)) {
                                                $sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
                                                return true;                                                
                                            }
                                            if((!$sender->getInventory()->contains(Item::get(264, 0, 32)) || !$sender->getInventory()->contains(Item::get(388, 0, 32)) || !$sender->getInventory()->contains(Item::get(266, 0, 32)) || !$sender->getInventory()->contains(Item::get(265, 0, 32)))) {
						$sender->sendMessage($this->plugin->formatMessage("Do przedluzenia waznosci gildii potrzebujesz 32 diamentow, 32 szmaragdow, 32 zlota, 32 zelaza."));                                                        
                                                return true;
                                            }
                                            
                                            $faction = $this->plugin->getPlayerFaction($sender->getName());
                                            
                                            $sender->getInventory()->removeItem(Item::get(264, 0, 32));
                                            $sender->getInventory()->removeItem(Item::get(388, 0, 32));
                                            $sender->getInventory()->removeItem(Item::get(266, 0, 32));
                                            $sender->getInventory()->removeItem(Item::get(265, 0, 32));     
                                            
                                            $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO expires (faction, time) VALUES (:faction, :time);");
                                            $stmt->bindValue(":faction", $faction);
                                            $stmt->bindValue(":time", $this->plugin->prefs->get("Expires"));
                                            $result = $stmt->execute();
                                            
                                            $sender->sendMessage($this->plugin->formatMessage("Waznosc gildii zostala przedluzona do 7 dni!"));
                                            return true;
                                        }
					if($args[0] == 'itemy') {
						$sender->sendMessage("§8[ §7----------- §a[Gildie] §7----------- §8]");
						$sender->sendMessage("§364 §7Diamentow");
						$sender->sendMessage("§364 §7Emeralow");
						$sender->sendMessage("§364 §7Zlota");
						$sender->sendMessage("§364 §7Zelaza");
						$sender->sendMessage("§364 §716 Perel (Sniezek)");
						$sender->sendMessage("§364 §764 TNT");
						$sender->sendMessage("§364 §764 Bookshelfow");
						$sender->sendMessage("§8[ §7----------- §a[Gildie] §7----------- §8]");
					}
                                        if(strtolower($args[0]) == "reload" || strtolower($args[0]) == "przeladuj") {
                                            if(count($args) > 1) {
                                                $sender->sendMessage($this->plugin->formatMessage("Uzyj: /g przeladuj"));
                                                return true;                                                
                                            }
                                            if(!$sender->hasPermission("factionspro.admin")) {
                                                $sender->sendMessage($this->plugin->formatMessage("Nie masz do tego uprawnien!"));
                                                return true;
                                            }
                                            $this->plugin->prefs->reload();
                                            $sender->sendMessage($this->plugin->formatMessage("Plik konfiguracyjny zostal przeladowany!"));
                                            return true;
                                        }
				/////////////////////////////// TELEPORTACJA ///////////////////////////////
										if(strtolower($args[0] == "tp")) {
						$faction = $args[1];
						$result = $this->plugin->db->query("SELECT * FROM home WHERE faction = '$faction';");
						$array = $result->fetchArray(SQLITE3_ASSOC);
						if($sender->hasPermission("gildie.tepanie") or $sender->isOp()){
						if(!empty($array)) {
							$sender->getPlayer()->teleport(new Vector3($array['x'], $array['y'], $array['z']));
							$sender->sendMessage($this->plugin->formatMessage("Teleportacja do gildi " . $args[1] . ""));
							return true;
						} else {
							$sender->sendMessage($this->plugin->formatMessage("Taka gildia niema ustawionego domu!"));
							}
						} 
						else {
							$sender->sendMessage($this->plugin->formatMessage("Nie mozesz teleportowac sie do kogos bazy!"));
										}
                    //Thanks To The original authors Tethered_
                    //Thank To The Supporter
                    //Big Thanks To NeuroBinds Project Corporation For Helping 64% Of The Code!
                }
				/////////////////////////////// EFEKTY ///////////////////////////////
				#do zrobienia: #
				# dodac administratorowi force kupowanie efektow #
				# dodac minusowe efekty dla innej gildi #
				/////////////////////////////// LISTA EFEKTÓW ///////////////////////////////
				if(strtolower($args[0] == "efekty")) {
					$sender->sendMessage("§8• §7Siła - §31 §7(5:00) §8•");
					$sender->sendMessage("§8• §7Siła II - §32 §7(5:00) §8•");
					$sender->sendMessage("§8• §7Szybkosc - §33 §7(5:00) §8•");
					$sender->sendMessage("§8• §7Szybkosc II - §34 §7(5:00) §8•");
					$sender->sendMessage("§8• §7Szybkie Kopanie - §35 §7(5:00) §8•");
					$sender->sendMessage("§8• §7Szybkie Kopanie II - §36 §7(5:00) §8•");
					$sender->sendMessage("§8• §7Wysokie Skakanie - §37 §7(5:00) §8•");
					$sender->sendMessage("§8• §7Wysokie Skakanie II - §38 §7(5:00) §8•");
				}
				/////////////////////////////// KOSZT EFEKTÓW ///////////////////////////////
				if(strtolower($args[0] == "koszt")) {
					$sender->sendMessage("§8• §7Numer §31 §7- §332 §7Emeraldy §8•");
					$sender->sendMessage("§8• §7Numer §32 §7- §364 §7Emeraldy §8•");
					$sender->sendMessage("§8• §7Numer §33 §7- §348 §7Emeraldów §8•");
					$sender->sendMessage("§8• §7Numer §34 §7- §364 §7Emeraldy §8•");
					$sender->sendMessage("§8• §7Numer §35 §7- §332 §7Emeraldy §8•");
					$sender->sendMessage("§8• §7Numer §36 §7- §364 §7Emeraldy §8•");
					$sender->sendMessage("§8• §7Numer §37 §7- §316 §7Emeraldow §8•");
					$sender->sendMessage("§8• §7Numer §38 §7- §324 §7Emeraldy §8•");
				}
				/////////////////////////////// DODAWNIE EFEKTÓW ///////////////////////////////
				if(strtolower($args[0] == "efekt")){
					if(!$this->plugin->isInFaction($sender->getName())){
					$sender->sendMessage($this->plugin->formatMessage("Musisz miec gildie!"));
				}
				}
				
			if(strtolower($args[0] == "efekt") && !isset($args[1])){
					$sender->sendMessage($this->plugin->formatMessage("Uzyj /g efekt <numer>"));
				}
				/////////////////////////////// SILA 1 ///////////////////////////////
					if(strtolower($args[0] == "efekt") && strtolower($args[1] == "1")){
						if($sender->getInventory()->contains(Item::get(388, 0, 32))){
							if($this->plugin->isLeader($player) == true){
								$faction = $this->plugin->getPlayerFaction($sender->getName());
								$array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
                            while($row = $array->fetchArray(SQLITE3_ASSOC)) {
                                if($this->plugin->getServer()->getPlayer($row["player"])) {
							$player = $sender->getName();
							$this->plugin->getServer()->getPlayer($row["player"])->addEffect(new EffectInstance(Effect::getEffect(5), 6000, 0));
							$this->plugin->getServer()->getPlayer($row["player"])->sendMessage($this->plugin->formatMessage($sender->getName() . " zakupił efekt numer 1 (Siła I)"));
							$sender->getInventory()->removeItem(Item::get(388, 0, 32));
				}
				}
							}
				else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic ten efekt musisz byc liderem!"));
				}
					}
									else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic efekt gildyjny (1) musisz miec 32 emeraldy!"));
				}
					}
					/////////////////////////////// SILA 2 ///////////////////////////////
						if(strtolower($args[0] == "efekt") && strtolower($args[1] == "2")){
						if($sender->getInventory()->contains(Item::get(388, 0, 64))){
							if($this->plugin->isLeader($player) == true){
								$r = count($args);
                        for ($i = 0; $i < $r - 1; $i = $i + 1) {
                        }
						$player = $sender->getName();
								$faction = $this->plugin->getPlayerFaction($sender->getName());
								$array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
                            while($row = $array->fetchArray(SQLITE3_ASSOC)) {
                                if($this->plugin->getServer()->getPlayer($row["player"])) {
							$this->plugin->getServer()->getPlayer($row["player"])->addEffect(new EffectInstance(Effect::getEffect(5), 6000, 1));
							$this->plugin->getServer()->getPlayer($row["player"])->sendMessage($this->plugin->formatMessage($sender->getName() . " zakupił efekt numer 2 (Siła II)"));
							$sender->getInventory()->removeItem(Item::get(388, 0, 64));
				}
				}
							}
				else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic ten efekt musisz byc liderem!"));
				}
					}
									else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic efekt gildyjny (2) musisz miec 64 emeraldy!"));
				}
					}
			/////////////////////////////// SZYBKOSC 1 ///////////////////////////////
						if(strtolower($args[0] == "efekt") && strtolower($args[1] == "3")){
						if($sender->getInventory()->contains(Item::get(388, 0, 48))){
							if($this->plugin->isLeader($player) == true){
								$r = count($args);
                        for ($i = 0; $i < $r - 1; $i = $i + 1) {
                        }
						$player = $sender->getName();
								$faction = $this->plugin->getPlayerFaction($sender->getName());
								$array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
                            while($row = $array->fetchArray(SQLITE3_ASSOC)) {
                                if($this->plugin->getServer()->getPlayer($row["player"])) {
							$this->plugin->getServer()->getPlayer($row["player"])->addEffect(new EffectInstance(Effect::getEffect(1), 6000, 0));
							$this->plugin->getServer()->getPlayer($row["player"])->sendMessage($this->plugin->formatMessage($sender->getName() . " zakupił efekt numer 3 (Szybkosc I)"));
							$sender->getInventory()->removeItem(Item::get(388, 0, 48));
				}
							}
				}
				else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic ten efekt musisz byc liderem!"));
				}
					}
									else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic efekt gildyjny (3) musisz miec 48 emeraldow!"));
				}
					}

			/////////////////////////////// SZYBKOSC 2 ///////////////////////////////
						if(strtolower($args[0] == "efekt") && strtolower($args[1] == "4")){
						if($sender->getInventory()->contains(Item::get(388, 0, 64))){
							if($this->plugin->isLeader($player) == true){
								$r = count($args);
                        for ($i = 0; $i < $r - 1; $i = $i + 1) {
                        }
						$player = $sender->getName();
								$faction = $this->plugin->getPlayerFaction($sender->getName());
								$array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
                            while($row = $array->fetchArray(SQLITE3_ASSOC)) {
                                if($this->plugin->getServer()->getPlayer($row["player"])) {
							$this->plugin->getServer()->getPlayer($row["player"])->addEffect(new EffectInstance(Effect::getEffect(1), 6000, 1));
							$this->plugin->getServer()->getPlayer($row["player"])->sendMessage($this->plugin->formatMessage($sender->getName() . " zakupił efekt numer 4 (Szybkosc II)"));
							$sender->getInventory()->removeItem(Item::get(388, 0, 64));
				}
				}
							}
				else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic ten efekt musisz byc liderem!"));
				}
					}
									else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic efekt gildyjny (4) musisz miec 64 emeraldy!"));
				}
					}
			/////////////////////////////// SZYBKIE KOPANIE 1 ///////////////////////////////
						if(strtolower($args[0] == "efekt") && strtolower($args[1] == "5")){
						if($sender->getInventory()->contains(Item::get(388, 0, 32))){
							if($this->plugin->isLeader($player) == true){
								$r = count($args);
                        for ($i = 0; $i < $r - 1; $i = $i + 1) {
                        }
						$player = $sender->getName();
								$faction = $this->plugin->getPlayerFaction($sender->getName());
								$array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
                            while($row = $array->fetchArray(SQLITE3_ASSOC)) {
                                if($this->plugin->getServer()->getPlayer($row["player"])) {
							$efekt = Effect::getEffect(3);
							$efekt->setDuration(6000);
							$efekt->setAmplifier(0);
							$this->plugin->getServer()->getPlayer($row["player"])->addEffect(new EffectInstance(Effect::getEffect(3), 6000, 0));
														$this->plugin->getServer()->getPlayer($row["player"])->sendMessage($this->plugin->formatMessage($sender->getName() . " zakupił efekt numer 5 (Szybkie Kopanie I)"));
							$sender->getInventory()->removeItem(Item::get(388, 0, 32));
				}
							}
				}
				else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic ten efekt musisz byc liderem!"));
				}
					}
									else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic efekt gildyjny (5) musisz miec 32 emeraldy!"));
				}
					}
			/////////////////////////////// SZYBKIE KOPANIE 2 ///////////////////////////////
						if(strtolower($args[0] == "efekt") && strtolower($args[1] == "6")){
						if($sender->getInventory()->contains(Item::get(388, 0, 64))){
							if($this->plugin->isLeader($player) == true){
								$r = count($args);
                        for ($i = 0; $i < $r - 1; $i = $i + 1) {
                        }
						$player = $sender->getName();
								$faction = $this->plugin->getPlayerFaction($sender->getName());
								$array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
                            while($row = $array->fetchArray(SQLITE3_ASSOC)) {
                                if($this->plugin->getServer()->getPlayer($row["player"])) {
							$this->plugin->getServer()->getPlayer($row["player"])->addEffect(new EffectInstance(Effect::getEffect(3), 6000, 1));
							$this->plugin->getServer()->getPlayer($row["player"])->sendMessage($this->plugin->formatMessage($sender->getName() . " zakupił efekt numer 6 (Szybkie Kopanie II)"));
							$sender->getInventory()->removeItem(Item::get(388, 0, 64));
				}
				}
							}
				else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic ten efekt musisz byc liderem!"));
				}
					}
									else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic efekt gildyjny (6) musisz miec 64 emeraldy!"));
				}
					}
			/////////////////////////////// WYSOKIE SKAKANIE 1 ///////////////////////////////
						if(strtolower($args[0] == "efekt") && strtolower($args[1] == "7")){
						if($sender->getInventory()->contains(Item::get(388, 0, 16))){
							if($this->plugin->isLeader($player) == true){
								$r = count($args);
                        for ($i = 0; $i < $r - 1; $i = $i + 1) {
                        }
						$player = $sender->getName();
								$faction = $this->plugin->getPlayerFaction($sender->getName());
								$array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
                            while($row = $array->fetchArray(SQLITE3_ASSOC)) {
                                if($this->plugin->getServer()->getPlayer($row["player"])) {
							$this->plugin->getServer()->getPlayer($row["player"])->addEffect(new EffectInstance(Effect::getEffect(8), 6000, 0));
							$this->plugin->getServer()->getPlayer($row["player"])->sendMessage($this->plugin->formatMessage($sender->getName() . " zakupił efekt numer 7 (Wysokie Skakanie I)"));
							$sender->getInventory()->removeItem(Item::get(388, 0, 16));
				}
				}
							}
				else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic ten efekt musisz byc liderem!"));
				}
					}
									else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic efekt gildyjny (7) musisz miec 16 emeraldów!"));
				}
					}
			/////////////////////////////// WYSOKIE SKAKANIE 2 ///////////////////////////////
						if(strtolower($args[0] == "efekt") && strtolower($args[1] == "8")){
						if($sender->getInventory()->contains(Item::get(388, 0, 24))){
							if($this->plugin->isLeader($player) == true){
								$r = count($args);
                        for ($i = 0; $i < $r - 1; $i = $i + 1) {
                        }
						$player = $sender->getName();
								$faction = $this->plugin->getPlayerFaction($sender->getName());
								$array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
                            while($row = $array->fetchArray(SQLITE3_ASSOC)) {
                                if($this->plugin->getServer()->getPlayer($row["player"])) {
							$this->plugin->getServer()->getPlayer($row["player"])->addEffect(new EffectInstance(Effect::getEffect(8), 6000, 1));
							$this->plugin->getServer()->getPlayer($row["player"])->sendMessage($this->plugin->formatMessage($sender->getName() . " zakupił efekt numer 8 (Wysokie Skakanie II)"));
							$sender->getInventory()->removeItem(Item::get(388, 0, 24));
				}
				}
							}
				else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic ten efekt musisz byc liderem!"));
				}
					}
									else{
					$sender->sendMessage($this->plugin->formatMessage("Aby kupic efekt gildyjny (8) musisz miec 24 emeraldy!"));
				}
					}
				/////////////////////////////// PVP ///////////////////////////////
				if(strtolower($args[0] == "pvp")){
				if(empty($args[1])){
				$sender->sendMessage($this->plugin->formatMessage("Uzyj: /g pvp on/off"));
				}
				if($this->plugin->isInFaction($sender->getName())){
				if($args[1] == "on"){
					if($this->plugin->isLeader($sender->getName())){
					$pvps = $this->plugin->pvp->get($this->plugin->getPlayerFaction($sender->getName()));
					$g = $this->plugin->getPlayerFaction($sender->getName());
					$this->plugin->pvp->set($g, $pvps+1);
					$this->plugin->pvp->save();
		$faction = $this->plugin->getPlayerFaction($sender->getName());
		$array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
		
		while($row = $array->fetchArray(SQLITE3_ASSOC)) {
		if($this->plugin->getServer()->getPlayer($row["player"])) {
				$this->plugin->getServer()->getPlayer($row["player"])->sendMessage($this->plugin->formatMessage("" . $sender->getName() . " §7włączył pvp w gildii!"));
				}
					}
					}
				else{
					$sender->sendMessage($this->plugin->formatMessage("Musisz być liderem gildii aby to wpisać!"));
				}
		}
						if($args[1] == "off"){
					if($this->plugin->isLeader($sender->getName())){
					$g = $this->plugin->getPlayerFaction($sender->getName());
					$this->plugin->pvp->set($g, "0");
					$this->plugin->pvp->save();
							$faction = $this->plugin->getPlayerFaction($sender->getName());
		$array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
		
		while($row = $array->fetchArray(SQLITE3_ASSOC)) {
				if($this->plugin->getServer()->getPlayer($row["player"])) {
				$this->plugin->getServer()->getPlayer($row["player"])->sendMessage($this->plugin->formatMessage("" . $sender->getName() . " §7wyłączył pvp w gildii!"));
				}
					}
					}
				else{
					$sender->sendMessage($this->plugin->formatMessage("Musisz być liderem gildii aby to wpisać!"));
				}
		}
			}
			else{
				$sender->sendMessage($this->plugin->formatMessage("Musisz mieć gildie!"));
			}
				}	
                   /////////////////////////////// SPRAWDZ ///////////////////////////////
				   
					if(strtolower($args[0]) == 'sprawdz'){
                        $x = floor($sender->getX());
						$y = floor($sender->getY());
						$z = floor($sender->getZ());
                        $fac = $this->plugin->factionFromPoint($x,$z);
                        if(!$this->plugin->isInPlot($x, $z)){
                            $sender->sendMessage($this->plugin->formatMessage("§7Ten teren jest wolny! Mozesz zalozyc tutaj gildie!"));
							return true;
                        }
                        $sender->sendMessage($this->plugin->formatMessage("§7Ten teren jest zajęty przez gildie:§3 $fac"));
                    }
															                   /////////////////////////////// LISTA ///////////////////////////////
				   
										if(strtolower($args[0]) == "members" || strtolower($args[0]) == "czlonkow" || strtolower($args[0]) == "lista" || strtolower($args[0]) == "list") {
                                            if(!isset($args[1])) {
                                            if(!$this->plugin->isInFaction($sender->getName())) {
                                                    $sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
                                                    return true;
                                            }
                                            $faction = $this->plugin->getPlayerFaction($sender->getName());
                                            $array = $this->plugin->db->query("SELECT * FROM master WHERE faction='$faction';");
                                            $members = array(
                                                "players" => "",
                                                "online" => 0,
                                                "all" => 0,
                                            );
                                            while($row = $array->fetchArray(SQLITE3_ASSOC)) {
                                                if($this->plugin->getServer()->getPlayer($row["player"]) instanceof Player) {
                                                    $members["players"] = $members["players"] . TextFormat::GREEN . $row["player"] . TextFormat::GRAY . ", ";
                                                    $members["online"]++;
                                                    $members["all"]++;
                                                } else {
                                                    $members["players"] = $members["players"] . TextFormat::GRAY . $row["player"] . ", ";
                                                    $members["all"]++;
                                                }
                                            }
                                            $count = strlen($members["players"]);
                                            $members["players"] = substr($members["players"], 0, $count - 2);
                                            $sender->sendMessage($this->plugin->formatMessage("Liczba czlonkow (Wszystkich: " . $members["all"]. ", Online: " . $members["online"] . "): " . $members["players"] . TextFormat::GRAY . "."));
                                            return true;
                                        }
										}
										/////////////////////////////// POWIEKSZANIE TERENU ///////////////////////////////
										if(strtolower($args[0]) == "powieksz"){
											$faction = $this->plugin->getPlayerFaction($sender->getName());
											$fac = $this->plugin->teren->get($faction);
											$result = $this->plugin->db->query("SELECT * FROM center WHERE faction='$faction';");
                                            $array = $result->fetchArray(SQLITE3_ASSOC);
                                            $x = $array["x"];
                                            $y = $array["y"];
                                            $z = $array["z"];
                                            $level = $array["world"];
											$size = $this->plugin->prefs->get("PlotSize2");
											$arm = ($size - 1) / 2;
											if($this->plugin->isLeader($player)){
										    if($fac == $this->plugin->prefs->get("PlotSize")){
											if($sender->getInventory()->contains(Item::get(264, 0, 32)) && $sender->getInventory()->contains(Item::get(388, 0, 32))){
											$this->plugin->db->query("DELETE FROM plots WHERE faction='$faction';");
											if(!$this->plugin->drawPlot($sender, $faction, $x, $y, $z, $level, $this->plugin->prefs->get("PlotSize2"))) {
											return true;
                                                        }
											$s = $this->plugin->prefs->get("PlotSize2");
											$this->plugin->teren->set($faction, $s);
											$this->plugin->teren->save();
											$sender->getInventory()->removeItem(Item::get(388, 0, 32));
											$sender->getInventory()->removeItem(Item::get(264, 0, 32));
											Server::getInstance()->broadcastMessage("§f• §7Gildia §3" . $faction . " §7powiększyła swój teren do §3" . $s . "§7x§3" . $s ."§7! §f•");
											$sender->getLevel()->setBlock(new Vector3($x + $arm, $y, $z + $arm), Block::get(49, 0));
											$sender->getLevel()->setBlock(new Vector3($x - $arm, $y, $z - $arm), Block::get(49, 0));
											$sender->getLevel()->setBlock(new Vector3($x - $arm, $y, $z + $arm), Block::get(49, 0));
											$sender->getLevel()->setBlock(new Vector3($x + $arm, $y, $z - $arm), Block::get(49, 0));
											}
											else{
											$sender->sendMessage($this->plugin->formatMessage("Aby powiekszyć teren do  §3" . $s . "§7x§3" . $s . " musisz zapłacić 32 emeraldy oraz 32 diamenty!"));	
											}
											}
										    if($fac == $this->plugin->prefs->get("PlotSize2")){
											$size = $this->plugin->prefs->get("PlotSize3");
											$arm = ($size - 1) / 2;
											if($sender->getInventory()->contains(Item::get(264, 0, 64)) && $sender->getInventory()->contains(Item::get(388, 0, 64))){
											$this->plugin->db->query("DELETE FROM plots WHERE faction='$faction';");
											if(!$this->plugin->drawPlot($sender, $faction, $x, $y, $z, $level, $this->plugin->prefs->get("PlotSize3"))) {
											return true;
											}
											$s = $this->plugin->prefs->get("PlotSize3");
											$this->plugin->teren->set($faction, $s);
											$this->plugin->teren->save();
											$sender->getInventory()->removeItem(Item::get(388, 0, 64));
											$sender->getInventory()->removeItem(Item::get(264, 0, 64));
											Server::getInstance()->broadcastMessage("§f• §7Gildia §3" . $faction . " §7powiększyła swój teren do §3" . $s . "§7x§3" . $s . "§7! §f•");
											$sender->getLevel()->setBlock(new Vector3($x + $arm, $y, $z + $arm), Block::get(49, 0));
											$sender->getLevel()->setBlock(new Vector3($x - $arm, $y, $z - $arm), Block::get(49, 0));
											$sender->getLevel()->setBlock(new Vector3($x - $arm, $y, $z + $arm), Block::get(49, 0));
											$sender->getLevel()->setBlock(new Vector3($x + $arm, $y, $z - $arm), Block::get(49, 0));
											}
											else{
											$sender->sendMessage($this->plugin->formatMessage("Aby powiekszyć teren do  §3" . $s . "§7x§3" . $s . " musisz zapłacić 64 emeraldy oraz 64 diamenty!"));	
											}
											}
											if($fac == $this->plugin->prefs->get("PlotSize3")){
											$sender->sendMessage($this->plugin->formatMessage("Teren twojej gildii jest juz powiekszony maksymalnie!"));
										}
										}
										}
										/////////////////////////////// POWIEKSZANIE TERENU ///////////////////////////////
																				} 
					}
	}
									else {
			$this->plugin->getServer()->getLogger()->info($this->plugin->formatMessage("Tej komendy mozna uzywac tylko w grze!"));
								}
				return false;
		}
	}