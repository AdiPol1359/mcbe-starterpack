<?php

namespace core\generator\object\tree;

use pocketmine\block\Block;
use pocketmine\block\Wood;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
use core\generator\object\Tree;

class OakTree extends Tree {

    public function __construct() {
        $this->trunkBlock = Block::LOG;
        $this->leafBlock = Block::LEAVES;
        $this->type = Wood::OAK;
    }

    public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random) {
        $this->treeHeight = $random->nextBoundedInt(3) + 4;
        parent::placeObject($level, $x, $y, $z, $random);
    }
}
