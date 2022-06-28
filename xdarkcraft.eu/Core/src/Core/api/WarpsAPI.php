<?php

namespace Core\api;

use pocketmine\math\Vector3;

use Core\Main;

class WarpsAPI {
	
	public function setWarp(string $name, Vector3 $pos) : void {
		$x = $pos->getX();
		$y = $pos->getY();
		$z = $pos->getZ();
		
		Main::getInstance()->getDb()->query("INSERT INTO warps (name, x, y, z) VALUES ('$name', '$x', '$y', '$z')");
	}
	
	public function removeWarp(string $name) : void {
		Main::getInstance()->getDb()->query("DELETE FROM warps WHERE name = '$name'");
	}
	
	public function getWarpPosition(string $name) : Vector3 {
		$array = Main::getInstance()->getDb()->query("SELECT * FROM warps WHERE name = '$name'")->fetchArray(SQLITE3_ASSOC);
		
		return new Vector3($array['x'], $array['y'], $array['z']);
	}
	
	public function isWarpExists(string $name) : bool {
		$array = Main::getInstance()->getDb()->query("SELECT * FROM warps WHERE name = '$name'")->fetchArray();
		
		return !empty($array);
	}
	
	public function getWarpByIndex(int $index) : ?string {
		$i = 0;
		
		$array = Main::getInstance()->getDb()->query("SELECT * FROM warps");
		
		while($row = $array->fetchArray(SQLITE3_ASSOC)) {
		 if($i == $index)
		  return $row['name'];
		  
		 $i++;
		}
		
		return null;
	}
}