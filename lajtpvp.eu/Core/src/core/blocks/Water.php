<?php

declare(strict_types=1);

namespace core\blocks;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifierFlattened;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Water as PMWater;

class Water extends PMWater {

    public function __construct(){
        parent::__construct(
            new BlockIdentifierFlattened(BlockLegacyIds::FLOWING_WATER, [BlockLegacyIds::STILL_WATER], 0),
            "Water",
            BlockBreakInfo::indestructible(500.0)
        );
    }

    public function onNearbyBlockChange() : void {}
}