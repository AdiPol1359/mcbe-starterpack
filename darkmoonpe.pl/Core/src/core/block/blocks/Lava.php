<?php

namespace core\block\blocks;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\Lava as PMLava;
use pocketmine\block\Water;

class Lava extends PMLava{

    public function tickRate() : int {
        return 20;
    }

    protected function checkForHarden(){
        $colliding = null;
        for($side = 1; $side <= 5; ++$side){
            $blockSide = $this->getSide($side);
            if($blockSide instanceof Water){
                $colliding = $blockSide;
                break;
            }
        }

        if($colliding !== null){
            if($this->getDamage() === 0){
                $this->liquidCollide($colliding, BlockFactory::get(Block::OBSIDIAN));
            }elseif($this->getDamage() <= 4){
                $this->liquidCollide($colliding, BlockFactory::get(Block::STONE));
            }
        }
    }
}