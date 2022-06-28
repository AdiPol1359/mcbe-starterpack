<?php

namespace Gildie\fakeinventory;

use pocketmine\Player;

use pocketmine\network\mcpe\protocol\types\WindowTypes;

class FakeInventoryAPI {
	
	private static $inv;
	
	public static function getInventory($player) : ?FakeInventory {
		return $player instanceof Player ? self::$inv[$player->getName()] : self::$inv[(string) $player];
	}
	
	public static function isOpening($player) : bool {
		return $player instanceof Player ? isset(self::$inv[$player->getName()]) : isset(self::$inv[(string) $player]);
	}
	
	public static function setInventory($player, FakeInventory $inv) : void {
		$player instanceof Player ? self::$inv[$player->getName()] = $inv : self::$inv[(string) $player] = $inv;
	}
	
	public static function unsetInventory($player) : void {
		if($player instanceof Player)
		 unset(self::$inv[$player->getName()]);
		else
		 unset(self::$inv[(string) $player]);
	}
}