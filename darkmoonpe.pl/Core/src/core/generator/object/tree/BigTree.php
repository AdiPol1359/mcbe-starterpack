<?php

namespace core\generator\object\tree;

use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
use core\generator\object\Tree;

class BigTree extends Tree {

    public function canPlaceObject(ChunkManager $level, int $x, int $y, int $z) : bool {
        return false;
    }

    public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random) {

    }
}