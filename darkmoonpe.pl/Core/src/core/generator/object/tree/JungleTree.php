<?php

namespace core\generator\object\tree;

use pocketmine\block\Block;
use pocketmine\block\Wood;
use core\generator\object\Tree;

class JungleTree extends Tree {

    public function __construct() {
        $this->trunkBlock = Block::LOG;
        $this->leafBlock = Block::LEAVES;
        $this->type = Wood::JUNGLE;
        $this->treeHeight = 5;
    }
}