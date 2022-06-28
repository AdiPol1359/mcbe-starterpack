<?php

namespace Core\api;

use Core\Main;

class PointsAPI {
	
	public function isInDatabase(string $nick) : bool {
		$result = Main::getInstance()->getDb()->query("SELECT * FROM points WHERE nick = '$nick'");
        return empty($result);
	}
	
	public function setDefault(string $nick) : void {
		Main::getInstance()->getDb()->query("INSERT INTO points (nick, points) VALUES ('$nick', '500')");
	}
	
	public function setPoints(string $nick, int $points) : void {
		Main::getInstance()->getDb()->query("UPDATE points SET points = '$points' WHERE nick = '$nick'");
	}
	
	public function addPoints(string $nick, int $count) : void {
		$count = $this->getPoints($nick) + $count;
		
		Main::getInstance()->getDb()->query("UPDATE points SET points = '$count' WHERE nick = '$nick'");
	}
	
	public function removePoints(string $nick, int $count) : void {
		$count = $this->getPoints($nick) - $count;

		if($count < 0)
		    $count = 0;

		Main::getInstance()->getDb()->query("UPDATE points SET points = '$count' WHERE nick = '$nick'");
	}
	
	public function getPoints(string $nick) :?int {
		return Main::getInstance()->getDb()->query("SELECT * FROM points WHERE nick = '$nick'")->fetchArray()["points"];

	}
}