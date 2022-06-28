<?php

namespace Core\api;

use pocketmine\Player;
use Core\Main;

class DropAPI {
	
	public function switchDrop(string $nick, string $drop) : void {
		
		$array = Main::getInstance()->getDb()->query("SELECT * FROM 'drop' WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);
		
		$array[$drop] == "on" ? Main::getInstance()->getDb()->query("UPDATE 'drop' SET '$drop' = 'off' WHERE nick = '$nick'") : Main::getInstance()->getDb()->query("UPDATE 'drop' SET '$drop' = 'on' WHERE nick = '$nick'");
	}
	
	public function isEnable(string $nick, string $drop) : bool {
		
		$array = Main::getInstance()->getDb()->query("SELECT * FROM 'drop' WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);
		
		return $array[$drop] == "on" ? true : false;
	}

	public function getChance(Player $player, int $chance) : int {
        if($player->hasPermission("PolishHard.drop.70"))
            return round($chance + ($chance*0.7));

        if($player->hasPermission("PolishHard.drop.50"))
            return round($chance + ($chance*0.5));

        if($player->hasPermission("PolishHard.drop.40"))
            return round($chance + ($chance*0.4));

	    return $chance;
    }
}