<?php

namespace Core\task;

use pocketmine\scheduler\Task;

use Core\Main;

class MuteTask extends Task {
	
	public function onRun(int $currentTick) : void {
		$db = Main::getInstance()->getDb();
		
		$result = $db->query("SELECT * FROM mute");
		
		while($row = $result->fetchArray(SQLITE3_ASSOC)) {
			
			if($row['date'] == null) continue;
			
		    $a_date = date("H:i:s");

            if(strtotime($a_date) > strtotime($row['date'])) {
            	$nick = $row['nick'];

            	$db->query("DELETE FROM mute WHERE nick = '$nick'");
            }
		}
	}
}