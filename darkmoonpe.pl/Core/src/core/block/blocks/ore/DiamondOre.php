<?php

namespace core\block\blocks\ore;

use pocketmine\block\BlockToolType;
use pocketmine\block\Solid;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\TieredTool;

class DiamondOre extends Solid {

    protected $id = self::DIAMOND_ORE;

    public function __construct(int $meta = 0) {
        $this->meta = $meta;
    }

    public function getHardness() : float {
        return 3;
    }

    public function getName() : string {
        return "Diamond Ore";
    }

    public function getToolType() : int {
        return BlockToolType::TYPE_PICKAXE;
    }

    public function getToolHarvestLevel() : int {
        return TieredTool::TIER_IRON;
    }

    public function getDropsForCompatibleTool(Item $item) : array {
        return [
            ItemFactory::get(Item::DIAMOND_ORE)
        ];
    }

    protected function getXpDropAmount() : int {
        return 0;
    }
}
