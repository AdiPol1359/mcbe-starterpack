<?php

namespace Core\task;

use pocketmine\scheduler\Task;

use Core\Main;

class BanTask extends Task {
	
	public function onRun(int $currentTick) : void {
		$db = Main::getInstance()->getDb();
		
		$result = $db->query("SELECT * FROM ban");
		
		while($row = $result->fetchArray(SQLITE3_ASSOC)) {
			if($row['date'] !== null) {
				
				$a_date = date("H:i:s");
				
				if(strtotime($a_date) > strtotime($row['date'])) {
					$nick = $row['nick'];
					
					$db->query("DELETE FROM ban WHERE nick = '$nick'");
				}
			}
		}
	}
}