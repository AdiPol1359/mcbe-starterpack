<?php

namespace Gildie\bossbar;

use pocketmine\Player;

class BossbarManager {
	
	private static $bossbar = [];
	
	public static function setBossbar(Player $player, Bossbar $bossbar) : void {
		self::$bossbar[$player->getName()] = $bossbar;
	}
	
	public static function unsetBossbar(Player $player) : void {
		unset(self::$bossbar[$player->getName()]);
	}
	
	public static function getBossbar(Player $player) : ?Bossbar {
		return self::$bossbar[$player->getName()] ?? null;
	}
}