<?php

declare(strict_types=1);

namespace core\blocks;

use pocketmine\block\BlockLegacyIds as Ids;
use pocketmine\block\BlockIdentifier as BID;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockToolType;
use pocketmine\block\Sand as PMSand;

class Sand extends PMSand {

    public function __construct() {
        parent::__construct(new BID(Ids::SAND, 0), "Sand", new BlockBreakInfo(0.5, BlockToolType::SHOVEL));
    }

    public function onNearbyBlockChange() : void{}
}