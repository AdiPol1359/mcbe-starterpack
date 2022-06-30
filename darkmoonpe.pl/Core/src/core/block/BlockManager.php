<?php

namespace core\block;

use core\block\blocks\Farmland;
use core\block\blocks\Hopper;
use core\block\blocks\Lava;
use core\block\blocks\Leaves;
use core\block\blocks\Melon;
use core\block\blocks\MobSpawner;
use core\block\blocks\ore\DiamondOre;
use core\block\blocks\ore\EmeraldOre;
use core\block\blocks\Sapling;
use core\block\blocks\SnowLayer;
use core\block\blocks\PrivateChest;
use pocketmine\block\BlockFactory;

class BlockManager {

    public static function init() : void {

        $blocks = [
            new Sapling(),
            new DiamondOre(),
            new EmeraldOre(),
            new SnowLayer(),
            new Hopper(),
            new Farmland(),
            new Leaves(),
            new MobSpawner(),
            new Lava(),
            new PrivateChest(),
            new Melon()
        ];

        foreach($blocks as $block)
            BlockFactory::registerBlock($block, true);
    }
}