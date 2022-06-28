<?php

namespace FactionsPro;

use pocketmine\scheduler\PluginTask;
use pocketmine\math\Vector3;
use pocketmine\block\Block;

class Task extends PluginTask {
    
    private $plugin;
    
    public function __construct(FactionMain $plugin) {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun($currentTick) {
        $faction = $this->plugin->db->query("SELECT * FROM expires;");
        while($row = $faction->fetchArray(SQLITE3_ASSOC)) {
            $factionName = $row["faction"];            
        
            $time = $row["time"];
            if($time <= 1) {
                $this->plugin->db->query("DELETE FROM plots WHERE faction='$factionName';");                                                          
                $this->plugin->db->query("DELETE FROM master WHERE faction='$factionName';");
                $this->plugin->db->query("DELETE FROM top WHERE faction='$factionName';");
                $this->plugin->db->query("DELETE FROM expires WHERE faction='$factionName';");
                $this->plugin->db->query("DELETE FROM home WHERE faction='$factionName';");
                
		$result = $this->plugin->db->query("SELECT * FROM center WHERE faction='$factionName';");
                $array = $result->fetchArray(SQLITE3_ASSOC);
                $x = $array["x"];
                $y = $array["y"];
                $z = $array["z"];
                $world = $array["world"];
                $this->plugin->getServer()->getLevelByName($world)->setBlock(new Vector3($array["x"], $array["y"], $array["z"]), Block::get(0));             
                $this->plugin->getServer()->broadcastMessage($this->plugin->formatMessage("Gildia " . strtoupper($factionName) . " nie przedluzyla waznosci i zostala rozwiazana! Lokalizacja x: " . floor($x) . " y: " . floor($y) . " z: " . floor($z) . " swiat: $world.", true));
                $this->plugin->db->query("DELETE FROM center WHERE faction='$factionName';");  
            }            
            
            $result = $this->plugin->db->query("SELECT * FROM center WHERE faction='$factionName';");
            $array = $result->fetchArray(SQLITE3_ASSOC); 
            
            if(!empty($array)) {             
                $x = $array["x"];
                $y = $array["y"];
                $z = $array["z"];
                $world = $array["world"];                 
                
                $createMaterial = explode(":", $this->plugin->prefs->get("CreateMaterial"));
                if($this->plugin->getServer()->getLevelByName($world)->getBlock(new Vector3($x, $y, $z))->getId() != $createMaterial[0]) {
                    $this->plugin->getServer()->getLevelByName($world)->setBlock(new Vector3($x, $y, $z), Block::get($createMaterial[0], $createMaterial[1]));       
                }

                $this->plugin->db->query("UPDATE expires SET time = time - '1' WHERE faction='$factionName';");
            }
        }
    }
}

