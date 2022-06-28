<?php

namespace FactionsPro;

use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\block\Snow;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\network\Network;
use pocketmine\network\protocol\ExplodePacket;

class FactionMain extends PluginBase implements Listener {
	
	public $db;
	public $prefs;
	public $getTop = array(11);
        
	public function onEnable() {
		
		@mkdir($this->getDataFolder());
		
		if(!file_exists($this->getDataFolder() . "BannedNames.txt")) {
			$file = fopen($this->getDataFolder() . "BannedNames.txt", "w");
			$txt = "Admin:admin:Staff:staff:Owner:owner:Builder:builder:Op:OP:op";
			fwrite($file, $txt);
		}
		
		$this->getServer()->getPluginManager()->registerEvents(new FactionListener($this), $this);
		$this->fCommand = new FactionCommands($this);
		$this->prefs = new Config($this->getDataFolder() . "Prefs.yml", CONFIG::YAML, array(
				"MaxFactionTagLength" => 4,
				"MaxFactionNameLength" => 24,
				"MaxPlayersPerFaction" => 30,
				"ClaimingEnabled" => true,
				"OnlyLeadersAndOfficersCanInvite" => true,
				"OfficersCanClaim" => true,
				"PlotSize" => 25,
				"PlotSize2" => 45,
				"PlotSize3" => 55,
                                "CreateMaterial" => "247:0",
                                "Expires" => 10080,
                                "WarEnabled" => true,
                                "WarLives" => 3,
                                "WarProtection" => 172800,
                                "WarWait" => 86400,
                                "WarWinPoints" => 1000,
                                "NightExplosionProtection" => "00-7",                   
                                "ObsidianDestroyEnabled" => true,
                                "ObsidianDestroyChance" => 30,
                                "RegionMinDistanceFromSpawn" => 350,
                                "RegionBlackListCommands" => array(
                                                        "/sethome",
                                                        "/ustawdom",
                                                        "/ctsethome"
                                ),                    
                                "BlackListWorlds" => array(
                                                         "world2"
                                ),
				"Member" => array(
						"claim" => false,
						"demote" => false,
						"home" => true,
						"invite" => false,
						"kick" => false,
						"motd" => false,
						"promote" => false,
						"sethome" => false,
						"unclaim" => false,
						"unsethome" => false
				),
				"Officer" => array(
						"claim" => true,
						"demote" => false,
						"home" => true,
						"invite" => true,
						"kick" => true,
						"motd" => true,
						"promote" => false,
						"sethome" => true,
						"unclaim" => true,
						"unsethome" => true
				)
		));
		$this->pvp = new Config($this->getDataFolder() . "/pvp.yml", Config::YAML);
		$this->zabicia = new Config($this->getDataFolder() . "/zabicia.yml", Config::YAML);
		$this->smierci = new Config($this->getDataFolder() . "/smierci.yml", Config::YAML);
		$this->teren = new Config($this->getDataFolder() . "teren.yml", Config::YAML);
		$this->db = new \SQLite3($this->getDataFolder() . "FactionsPro.db");
		$this->db->exec("CREATE TABLE IF NOT EXISTS master (player TEXT PRIMARY KEY COLLATE NOCASE, faction TEXT, rank TEXT, chat BOOLEAN, chat_type TEXT, chat_color TEXT);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS confirm (player TEXT PRIMARY KEY COLLATE NOCASE, faction TEXT, invitedby TEXT, timestamp INT);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS motdrcv (player TEXT PRIMARY KEY, timestamp INT);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS motd (faction TEXT PRIMARY KEY, message TEXT);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS alliance (player TEXT PRIMARY KEY COLLATE NOCASE, faction TEXT, requestedby TEXT, timestamp INT);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS plots(faction TEXT PRIMARY KEY, x1 INT, z1 INT, x2 INT, z2 INT);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS home(faction TEXT PRIMARY KEY, x INT, y INT, z INT, world VARCHAR);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS top(faction TEXT PRIMARY KEY, points INT, wins INT);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS expires(faction TEXT PRIMARY KEY, time INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS center(faction TEXT PRIMARY KEY, lives INT, timeWarProtection INT, timeWarWait INT, x INT, y INT, z INT, world VARCHAR);"); 
		$this->db->exec("CREATE TABLE IF NOT EXISTS allies(ID INT PRIMARY KEY,faction1 TEXT, faction2 TEXT);");		
		
				$task = new Task($this);
                $this->getServer()->getScheduler()->scheduleDelayedRepeatingTask($task, 20*60, 20*60);
                date_default_timezone_set('Europe/Warsaw');
	}
		
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		$this->fCommand->onCommand($sender, $command, $label, $args);
	}
	
		    public function getAPI()
    {
        return Server::getInstance()->getPluginManager()->getPlugin("SystemOsiagniec");
    }
        
        public function packetFakeExplode($level, $x, $y, $z, $radius) {
            $pk = new ExplodePacket;
            $pk->x = $x;
            $pk->y = $y;
            $pk->z = $z;
            $pk->radius = $radius; 
            $pk->records = [new Vector3($x, $y, $z)];
            Server::broadcastPacket($this->getServer()->getLevelByName($level)->getChunkPlayers($x >> 4, $z >> 4), $pk->setChannel(Network::$BATCH_THRESHOLD));            
        }
        
        public function getFactionWins($faction) {
            $faction = $this->db->query("SELECT * FROM top WHERE LOWER(faction)='$faction';");
            $array = $faction->fetchArray(SQLITE3_ASSOC);
            return $array["wins"];              
        }
		public function setAllies($faction1, $faction2){
        $stmt = $this->db->prepare("INSERT INTO allies (faction1, faction2) VALUES (:faction1, :faction2);");  
        $stmt->bindValue(":faction1", $faction1);
		$stmt->bindValue(":faction2", $faction2);
		$result = $stmt->execute();
    }
	public function areAllies($faction1, $faction2){
        $result = $this->db->query("SELECT * FROM allies WHERE faction1 = '$faction1' AND faction2 = '$faction2';");
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        if(empty($resultArr)==false){
            return true;
        } 
    }
	public function deleteAllies($faction1, $faction2){
        $stmt = $this->db->prepare("DELETE FROM allies WHERE faction1 = '$faction1' AND faction2 = '$faction2';");   
		$result = $stmt->execute();
    }
 
        public function getFactionTimeWarWait($faction) {
            $faction = $this->db->query("SELECT * FROM center WHERE faction='$faction';");
            $array = $faction->fetchArray(SQLITE3_ASSOC);
            return $array["timeWarWait"];               
        }        
        
        public function getFactionTimeWarProtection($faction) {
            $faction = $this->db->query("SELECT * FROM center WHERE faction='$faction';");
            $array = $faction->fetchArray(SQLITE3_ASSOC);
            return $array["timeWarProtection"];               
        }
        
        public function getFactionWarLives($faction) {
            $faction = strtolower($faction);
            $faction = $this->db->query("SELECT * FROM center WHERE LOWER(faction)='$faction';");
            $array = $faction->fetchArray(SQLITE3_ASSOC);
            return $array["lives"];            
        }
        
        public function getFactionChatType($player) {
            $faction = $this->db->query("SELECT * FROM master WHERE player='$player';");
            $array = $faction->fetchArray(SQLITE3_ASSOC);
            return $array["chat_type"];
        }
        
        public function getFactionChatColor($player) {
            $faction = $this->db->query("SELECT * FROM master WHERE player='$player';");
            $array = $faction->fetchArray(SQLITE3_ASSOC);
            return $array["chat_color"];            
        }
	
        public function getFactionChat($player) {
            $faction = $this->db->query("SELECT * FROM master WHERE player='$player';");
            $array = $faction->fetchArray(SQLITE3_ASSOC);
            return $array["chat"];
        }
        
        public function getFactionTop() {
            $faction = $this->db->query("SELECT * FROM top ORDER BY points DESC LIMIT 10");
            $this->getTop[11] = 0;
            while($row = $faction->fetchArray(SQLITE3_BOTH)) {
                $this->getTop[11]++;
                $this->getTop[$this->getTop[11]] = "&7Gildia &a" . strtoupper($row[0]) . "&7 (Punkty &a$row[1]&7)";
            }
        }
        
        public function getFactionPoints($faction) {
            $faction = $this->db->query("SELECT * FROM top WHERE LOWER(faction)='$faction';");
            $array = $faction->fetchArray(SQLITE3_ASSOC);
            return $array["points"];
        }
        
	public function isInFaction($player) {
		$player = strtolower($player);
		$result = $this->db->query("SELECT * FROM master WHERE player='$player';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}
	
	public function isLeader($player) {
		$faction = $this->db->query("SELECT * FROM master WHERE player='$player';");
		$factionArray = $faction->fetchArray(SQLITE3_ASSOC);
		return $factionArray["rank"] == "Leader";
	}
	
	public function isOfficer($player) {
		$faction = $this->db->query("SELECT * FROM master WHERE player='$player';");
		$factionArray = $faction->fetchArray(SQLITE3_ASSOC);
		return $factionArray["rank"] == "Officer";
	}
	
	public function isMember($player) {
		$faction = $this->db->query("SELECT * FROM master WHERE player='$player';");
		$factionArray = $faction->fetchArray(SQLITE3_ASSOC);
		return $factionArray["rank"] == "Member";
	}
	
	public function getRank($player) {
		$faction = $this->db->query("SELECT * FROM master WHERE player='$player';");
		$factionArray = $faction->fetchArray(SQLITE3_ASSOC);
		return $factionArray["rank"];
	}
	
	public function hasPermission($player, $command) {
		$rank = $this->getRank($player);
		return $this->prefs->get("$rank")["$command"];
	}
	
	public function getPlayerFaction($player) {
		$faction = $this->db->query("SELECT * FROM master WHERE player='$player';");
		$factionArray = $faction->fetchArray(SQLITE3_ASSOC);
		return $factionArray["faction"];
	}
	
	public function getLeader($faction) {
		$leader = $this->db->query("SELECT * FROM master WHERE LOWER(faction)='$faction' AND rank='Leader';");
		$leaderArray = $leader->fetchArray(SQLITE3_ASSOC);
		return $leaderArray['player'];
	}
	
	public function factionExists($faction) {
		$result = $this->db->query("SELECT * FROM master WHERE LOWER(faction)='$faction';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}
	
	public function sameFaction($player1, $player2) {
		$faction = $this->db->query("SELECT * FROM master WHERE player='$player1';");
		$player1Faction = $faction->fetchArray(SQLITE3_ASSOC);
		$faction = $this->db->query("SELECT * FROM master WHERE player='$player2';");
		$player2Faction = $faction->fetchArray(SQLITE3_ASSOC);
		return $player1Faction["faction"] == $player2Faction["faction"];
	}
	
	public function getNumberOfPlayers($faction) {
		$query = $this->db->query("SELECT COUNT(*) as count FROM master WHERE LOWER(faction)='$faction';");
		$number = $query->fetchArray();
		return $number['count'];
	}
	
	public function isFactionFull($faction) {
		return $this->getNumberOfPlayers($faction) >= $this->prefs->get("MaxPlayersPerFaction");
	}
	
	public function isNameBanned($name) {
		$bannedNames = explode(":", file_get_contents($this->getDataFolder() . "BannedNames.txt"));
		return in_array($name, $bannedNames);
	}
	
public function newPlot($faction, $x1, $z1, $x2, $z2) {
		$stmt = $this->db->prepare("INSERT OR REPLACE INTO plots (faction, x1, z1, x2, z2) VALUES (:faction, :x1, :z1, :x2, :z2);");
		$stmt->bindValue(":faction", $faction);
		$stmt->bindValue(":x1", $x1);
		$stmt->bindValue(":z1", $z1);
		$stmt->bindValue(":x2", $x2);
		$stmt->bindValue(":z2", $z2);
		$result = $stmt->execute();
	}
	public function drawPlot($sender, $faction, $x, $y, $z, $level, $size) {            
            
		$arm = ($size - 1) / 2;
		$block = new Snow();
		if($this->cornerIsInPlot($x + $arm, $z + $arm, $x - $arm, $z - $arm)) {
			//$claimedBy = $this->factionFromPoint($x, $z);
			$sender->sendMessage($this->formatMessage("Ten teren jest zajety lub zbyt blisko innej gildii!"));
			return false;
		}
		/*$level->setBlock(new Vector3($x + $arm, $y, $z + $arm), $block);
		$level->setBlock(new Vector3($x - $arm, $y, $z - $arm), $block);   */      
                
		$this->newPlot($faction, $x + $arm, $z + $arm, $x - $arm, $z - $arm);
		return true;
	}
	
	public function isInPlot($x, $z) {
		$result = $this->db->query("SELECT * FROM plots WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2;");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
	}
	
	public function factionFromPoint($x,$z) {
		$result = $this->db->query("SELECT * FROM plots WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2;");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return $array["faction"];
	}
	
	public function inOwnPlot($player, $x, $z) {
		$playerName = $player->getName();
		return $this->getPlayerFaction($playerName) == $this->factionFromPoint($x, $z);
	}
	
	public function pointIsInPlot($x,$z) {
                
		$result = $this->db->query("SELECT * FROM plots WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2;");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return !empty($array);
	}
	
	public function cornerIsInPlot($x1, $z1, $x2, $z2) {
		return($this->pointIsInPlot($x1, $z1) || $this->pointIsInPlot($x1, $z2) || $this->pointIsInPlot($x2, $z1) || $this->pointIsInPlot($x2, $z2));
	}
	
	public function formatMessage($string, $confirm = false) {
		if($confirm) {
			return TextFormat::WHITE . "§f• §8> " . TextFormat::GREEN . "§8[§aGildie§8] " . TextFormat::GRAY . "$string " . TextFormat::WHITE . "•";
		} else {	
			return TextFormat::WHITE . "§f• §8> " . TextFormat::GREEN . "§8[§aGildie§8] " . TextFormat::GRAY . "$string " . TextFormat::WHITE . "•";
		}
	}
	
	public function motdWaiting($player) {
		$stmt = $this->db->query("SELECT * FROM motdrcv WHERE player='$player';");
		$array = $stmt->fetchArray(SQLITE3_ASSOC);
		return !empty($array);
	}
	
	public function getMOTDTime($player) {
		$stmt = $this->db->query("SELECT * FROM motdrcv WHERE player='$player';");
		$array = $stmt->fetchArray(SQLITE3_ASSOC);
		return $array['timestamp'];
	}
	
	public function setMOTD($faction, $player, $msg) {
		$stmt = $this->db->prepare("INSERT OR REPLACE INTO motd (faction, message) VALUES (:faction, :message);");
		$stmt->bindValue(":faction", $faction);
		$stmt->bindValue(":message", $msg);
		$result = $stmt->execute();
		
		$this->db->query("DELETE FROM motdrcv WHERE player='$player';");
	}
	
	public function updateTag($player) {
		$p = $this->getServer()->getPlayer($player);
		if(!$this->isInFaction($player)) {
			$p->setNameTag($player);
		} elseif($this->isLeader($player)) {
			$p->setNameTag("**[" . $this->getPlayerFaction($player) . "] " . $player);
		} elseif($this->isOfficer($player)) {
			$p->setNameTag("*[" . $this->getPlayerFaction($player) . "] " . $player);
		} elseif($this->isMember($player)) {
			$p->setNameTag("[" . $this->getPlayerFaction($player) . "] " . $player);
		}
	}
	    public function getFaction($player) {
        $faction = $this->db->query("SELECT * FROM master WHERE player='$player';");
        $factionArray = $faction->fetchArray(SQLITE3_ASSOC);
        return $factionArray["faction"];
    }
	    public function getPlayersInFactionByRank($s, $faction, $rank) {

        if ($rank != "Leader") {
            $rankname = $rank . 's';
        } else {
            $rankname = $rank;
        }
        $team = "";
        $result = $this->db->query("SELECT * FROM master WHERE faction='$faction' AND rank='$rank';");
        $row = array();
        $i = 0;

        while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
            $row[$i]['player'] = $resultArr['player'];
            if ($this->getServer()->getPlayerExact($row[$i]['player']) instanceof Player) {
                $team .= TextFormat::GREEN . " +" . TextFormat::GREEN . $row[$i]['player'] . TextFormat::GREEN . "" . TextFormat::RESET . TextFormat::WHITE . "" . TextFormat::RESET;
            } else {
                $team .= TextFormat::RED . " -" . TextFormat::RED . $row[$i]['player'] . TextFormat::RED . "" . TextFormat::RESET . TextFormat::WHITE . "" . TextFormat::RESET;
            }
            $i = $i + 1;
        }

        $s->sendMessage("", true);
        $s->sendMessage($team);
    }
	    public function inOwnPlots($player) {
        $playerName = $player->getName();
        $x = $player->getFloorX();
        $z = $player->getFloorZ();
        return $this->getPlayerFaction($playerName) == $this->factionFromPoint($x, $z);
    }
	public function onDisable() {
		$this->db->close();
	}
}
