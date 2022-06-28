<?php

namespace Core\api;

use pocketmine\Player;

use Core\Main;

class StatsAPI {
	
	public function isInDatabase(string $nick) : bool {
		$result = Main::getInstance()->getDb()->query("SELECT * FROM stats WHERE nick = '$nick'");
		
		return empty($result);
	}
	
	public function setDefault(string $nick) : void {
		Main::getInstance()->getDb()->query("INSERT INTO stats (nick, kills, deaths, koxy, refy, perly) VALUES ('$nick', '0', '0', '0', '0', '0')");
	}
	
	public function addKill(string $nick) : void {
		Main::getInstance()->getDb()->query("UPDATE stats SET kills = kills + '1' WHERE nick = '$nick'");
	}
	
	public function addDeath(string $nick) : void {
		Main::getInstance()->getDb()->query("UPDATE stats SET deaths = deaths + '1' WHERE nick = '$nick'");
	}
	
	public function addKoxy(string $nick) : void {
		Main::getInstance()->getDb()->query("UPDATE stats SET koxy = koxy + '1' WHERE nick = '$nick'");
	}
	
	public function addRefy(string $nick) : void {
		Main::getInstance()->getDb()->query("UPDATE stats SET refy = refy + '1' WHERE nick = '$nick'");
	}
	
	public function addPerly(string $nick) : void {
		Main::getInstance()->getDb()->query("UPDATE stats SET perly = perly + '1' WHERE nick = '$nick'");
	}
	
	public function getKills(string $nick) : ?int {
		$array = Main::getInstance()->getDb()->query("SELECT * FROM stats WHERE nick = '$nick'")->fetchArray();
		
		return $array['kills'];
	}
	
	public function getDeaths(string $nick) : ?int {
		$array = Main::getInstance()->getDb()->query("SELECT * FROM stats WHERE nick = '$nick'")->fetchArray();
		
		return $array['deaths'];
	}
	
	public function getKoxy(string $nick) : ?int {
		$array = Main::getInstance()->getDb()->query("SELECT * FROM stats WHERE nick = '$nick'")->fetchArray();
		
		return $array['koxy'];
	}
	
	public function getRefy(string $nick) : ?int {
		$array = Main::getInstance()->getDb()->query("SELECT * FROM stats WHERE nick = '$nick'")->fetchArray();
		
		return $array['refy'];
	}
	
	public function getPerly(string $nick) : ?int {
		$array = Main::getInstance()->getDb()->query("SELECT * FROM stats WHERE nick = '$nick'")->fetchArray();
		
		return $array['perly'];
	}
}