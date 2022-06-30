<?php

namespace core\generator\object\tree;

use pocketmine\block\Block;
use pocketmine\block\Wood;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
use core\generator\object\Tree;

class BirchTree extends Tree {

    protected bool $superBirch = false;

    public function __construct(bool $superBirch = false) {
        $this->trunkBlock = Block::LOG;
        $this->leafBlock = Block::LEAVES;
        $this->type = Wood::BIRCH;
        $this->superBirch = $superBirch;
    }

    public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random) {
        $this->treeHeight = $random->nextBoundedInt(3) + 5;
        if($this->superBirch) {
            $this->treeHeight += 5;
        }
        parent::placeObject($level, $x, $y, $z, $random);
    }
}