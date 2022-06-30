<?php

namespace core\block\blocks\ore;

use pocketmine\block\BlockToolType;
use pocketmine\block\Solid;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\TieredTool;

class EmeraldOre extends Solid {

    protected $id = self::EMERALD_ORE;

    public function __construct(int $meta = 0) {
        $this->meta = $meta;
    }

    public function getName() : string {
        return "Emerald Ore";
    }

    public function getToolType() : int {
        return BlockToolType::TYPE_PICKAXE;
    }

    public function getToolHarvestLevel() : int {
        return TieredTool::TIER_IRON;
    }

    public function getHardness() : float {
        return 3;
    }

    public function getDropsForCompatibleTool(Item $item) : array {
        return [
            ItemFactory::get(Item::EMERALD_ORE)
        ];
    }

    protected function getXpDropAmount() : int {
        return 0;
    }
}
