<?php

declare(strict_types=1);

namespace core\blocks;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifierFlattened;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Lava as PMLava;

class Lava extends PMLava {

    public function __construct() {
        parent::__construct(
            new BlockIdentifierFlattened(BlockLegacyIds::FLOWING_LAVA, [BlockLegacyIds::STILL_LAVA], 0),
            "Lava",
            BlockBreakInfo::indestructible(500.0)
        );
    }

    public function onNearbyBlockChange() : void {
    }
}