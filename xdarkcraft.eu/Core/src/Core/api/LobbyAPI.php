<?php

declare(strict_types=1);

namespace Core\api;

use pocketmine\Player;
use pocketmine\utils\Config;
use Core\Main;

class LobbyAPI {
	
	public static $config;
	
	public static function init() : void {
		self::$config = new Config(Main::getInstance()->getDataFolder(). "lobby.yml", Config::YAML, [
		    "status" => false,
		    "date" => null,
		    "players" => [],
            "messageEvery" => 5,
            "message" => "sample message"
		]);
	}

	public static function getMessageEvery() : int {
        return self::$config->get("messageEvery");
    }

    public static function getMessage() {
	    $message = self::$config->get("message");

	    if(!$message)
	        return null;

	    return $message;
    }
	
	public static function getLobbyPlayers() : array {
		return self::$config->get("players");
	}
	
	public static function setLobby(bool $status = true) : void {
		self::$config->set("status", $status);
		if(!$status)
		 self::$config->set("date", null);
		self::$config->save();
	}
	
	public static function setLobbyDate(?string $date = null) : void {
		self::$config->set("date", $date);
		self::$config->save();
	}
	
	public static function getLobbyDate() : ?string {
		$date = self::$config->get("date");
		if(!$date)
		 return null;
		return $date;
	}
	
	public static function isLobbyEnabled() : bool {
		return self::$config->get("status");
	}
	
	public static function addPlayer(string $nick) : void {
		$nick = strtolower($nick);
		$players = self::$config->get("players");
		if(in_array($nick, $players))
		 return;
		$players[] = $nick;
		self::$config->set("players", $players);
		self::$config->save();
	}
	
	public static function removePlayer(string $nick) : void {
		$nick = strtolower($nick);
		$players = self::$config->get("players");
		unset($players[array_search($nick, $players)]);
		
		$newArray = [];
		
		foreach($players as $player)
		 $newArray[] = $player;
		
		self::$config->set("players", $newArray);
		self::$config->save();
	}
	
	public static function isInLobby(string $nick) : bool {
		$nick = strtolower($nick);
		$players = self::getLobbyPlayers();
		return in_array($nick, $players);
	}
	
	public static function dateFormat() : string {
		$date = self::getLobbyDate();
		
		if($date == null)
		 return "§4Coming Soon...";
		
		$time = strtotime($date) - time();
 	
		$days = intval(intval($time) / (3600*24)); 
		$hours = (intval($time) / 3600) % 24;
		$minutes = (intval($time) / 60) % 60;
		$seconds = intval($time) % 60;
		
		if($days < 10)
		 $days = "0".$days;
		
		if($hours < 10)
		 $hours = "0".$hours;
		
		if($minutes < 10)
		 $minutes = "0".$minutes;
		
		if($seconds < 10)
		 $seconds = "0".$seconds;
		 
		 return "§7Start za: §4{$days} §7dni, §4{$hours} §7godzin, §4{$minutes} §7minut i §4{$seconds} §7sekund";
	}
}