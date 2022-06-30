<?php

namespace core\tile;

use core\tile\tiles\Hopper;
use core\tile\tiles\MobSpawner;
use pocketmine\tile\Tile;
use ReflectionException;

class TileManager {

    public static function init() : void {
        try {
            Tile::registerTile(Hopper::class, [Tile::HOPPER, "minecraft:hopper"]);
            Tile::registerTile(MobSpawner::class, [Tile::MOB_SPAWNER, "minecraft:mob_spawner"]);
        } catch(ReflectionException $e) {
            var_dump($e);
        }
    }
}