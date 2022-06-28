<?php

namespace Core\api;

use pocketmine\Player;

use Core\Main;

class BanAPI {
	
	public function setBan(string $nick, string $reason, string $adminNick) : void {
		Main::getInstance()->getDb()->query("INSERT INTO ban (nick, reason, adminNick) VALUES ('$nick', '$reason', '$adminNick')");
	}
	
	public function setTempBan(string $nick, string $reason, string $adminNick, int $time) : void {
		$date = date("H:i:s");
  
  $date = date('d.m.Y H:i:s', strtotime($date) + $time);
		
		Main::getInstance()->getDb()->query("INSERT INTO ban (nick, reason, adminNick, date) VALUES ('$nick', '$reason', '$adminNick', '$date')");
	}
	
	public function setIpBan(string $reason, string $adminNick, string $ip) : void {
		Main::getInstance()->getDb()->query("INSERT INTO ban (reason, adminNick, ip) VALUES ('$reason', '$adminNick', '$ip')");
	}
	
	public function unban(string $nick) : void {
		Main::getInstance()->getDb()->query("DELETE FROM ban WHERE nick = '$nick'");
	}
	
	public function unbanIp(string $ip) : void {
		Main::getInstance()->getDb()->query("DELETE FROM ban WHERE ip = '$ip'");
	}
	
	public function isBanned(string $nick) : bool {
		return !empty(Main::getInstance()->getDb()->query("SELECT * FROM ban WHERE nick = '$nick'")->fetchArray());
	}
	
	public function isIpBanned(string $ip) : bool {
		return !empty(Main::getInstance()->getDb()->query("SELECT * FROM ban WHERE ip = '$ip'")->fetchArray());
	}
	
	public function getBanMessage(Player $player) : string {
		
		$nick = $player->getName();
		
		if($this->isIpBanned($player->getAddress()))
		 $array = Main::getInstance()->getDb()->query("SELECT * FROM ban WHERE ip = '{$player->getAddress()}'")->fetchArray(SQLITE3_ASSOC);
		else
		 $array = Main::getInstance()->getDb()->query("SELECT * FROM ban WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);
		
		$adminNick = $array['adminNick'];
		$reason = $array['reason'];
		$array['date'] == null ? $date = "NIGDY" : $date = date("d.m.Y | H:i:s", strtotime($array['date']));
		
		if($this->isIpBanned($player->getAddress()))
		 return "§8§l>§r §7Na IP przez: §4".$adminNick."\n".
		       "§8§l>§r §7Powód: §4".$reason."\n".
		       "§8§l>§r §7Wygasa: §4".$date;
		else
		 return "§8§l>§r §7Na nick przez: §4".$adminNick."\n".
		       "§8§l>§r §7Powód: §4".$reason."\n".
		       "§8§l>§r §7Wygasa: §4".$date;
	}
}