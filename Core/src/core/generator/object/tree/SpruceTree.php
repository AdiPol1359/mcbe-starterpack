<?php

namespace core\generator\object\tree;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\Wood;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

use core\generator\object\Tree;

use function abs;

class SpruceTree extends Tree {

    public function __construct() {
        $this->trunkBlock = Block::LOG;
        $this->leafBlock = Block::LEAVES;
        $this->type = Wood::SPRUCE;
        $this->treeHeight = 7;
    }

    public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random) {
        $this->treeHeight = $random->nextBoundedInt(4) + 6;

        $topSize = $this->treeHeight - (1 + $random->nextBoundedInt(2));
        $lRadius = 2 + $random->nextBoundedInt(2);

        parent::placeObject($level, $x, $y, $z, $random);

        $radius = $random->nextBoundedInt(2);
        $maxR = 1;
        $minR = 0;

        for($yy = 0; $yy <= $topSize; ++$yy) {
            $yyy = $y + $this->treeHeight - $yy;

            for($xx = $x - $radius; $xx <= $x + $radius; ++$xx) {
                $xOff = abs($xx - $x);
                for($zz = $z - $radius; $zz <= $z + $radius; ++$zz) {
                    $zOff = abs($zz - $z);
                    if($xOff === $radius && $zOff === $radius && $radius > 0) {
                        continue;
                    }

                    if(!BlockFactory::$solid[$level->getBlockIdAt($xx, $yyy, $zz)]) {
                        $level->setBlockIdAt($xx, $yyy, $zz, $this->leafBlock);
                        $level->setBlockDataAt($xx, $yyy, $zz, $this->type);
                    }
                }
            }

            if($radius >= $maxR) {
                $radius = $minR;
                $minR = 1;
                if(++$maxR > $lRadius) {
                    $maxR = $lRadius;
                }
            } else {
                ++$radius;
            }
        }
    }
}