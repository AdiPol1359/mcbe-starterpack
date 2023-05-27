<?php

declare(strict_types=1);

namespace core\blocks;

use pocketmine\block\Bamboo as PMBamboo;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier as BID;
use pocketmine\block\BlockLegacyIds as Ids;
use pocketmine\block\BlockToolType;

class Bamboo extends PMBamboo {

    public function __construct() {
        parent::__construct(new BID(Ids::BAMBOO, 0), "Bamboo", new BlockBreakInfo(2.0 /* 1.0 in PC */, BlockToolType::AXE));
    }

    public function ticksRandomly() : bool {
        return false;
    }
}