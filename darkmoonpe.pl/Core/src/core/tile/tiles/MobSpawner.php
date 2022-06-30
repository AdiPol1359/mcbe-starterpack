<?php

namespace core\tile\tiles;

use pocketmine\tile\MobSpawner as PMMobSpawner;

class MobSpawner extends PMMobSpawner {
    protected $minSpawnDelay = 10;
    protected $maxSpawnDelay = 20;
}