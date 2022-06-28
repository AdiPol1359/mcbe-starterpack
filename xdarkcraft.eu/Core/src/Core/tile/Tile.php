<?php

namespace Core\tile;

use pocketmine\tile\Tile as PMTile;

abstract class Tile extends PMTile {
	
	public const BEACON = "Beacon";
	
	public static function init() : void {
		self::registerTile(Beacon::class, [self::BEACON, "minecraft:beacon"]);
	}
}