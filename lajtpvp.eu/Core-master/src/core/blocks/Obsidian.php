<?php

declare(strict_types=1);

namespace core\blocks;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\item\ToolTier;

class Obsidian extends Opaque {

    // $this->registerAllMeta(new Opaque(new BID(Ids::OBSIDIAN, 0), "Obsidian", new BlockBreakInfo(35.0 /* 50 in PC */, BlockToolType::PICKAXE, ToolTier::DIAMOND()->getHarvestLevel(), 6000.0)));

    public function __construct() {
        parent::__construct(
            new BlockIdentifier(BlockLegacyIds::OBSIDIAN, 0),
            "Obsidian",
            new BlockBreakInfo(35.0 /* 50 in PC */, BlockToolType::PICKAXE, ToolTier::DIAMOND()->getHarvestLevel(), 6000.0)
        );
    }

    public function getBlastResistance() : float {
        return 61.5;
    }
}