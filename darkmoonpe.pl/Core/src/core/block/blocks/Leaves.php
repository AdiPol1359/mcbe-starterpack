<?php

namespace core\block\blocks;

use pocketmine\block\BlockToolType;
use pocketmine\block\Leaves as PMLeaves;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class Leaves extends PMLeaves{
    public function getDrops(Item $item) : array{
        if(($item->getBlockToolType() & BlockToolType::TYPE_SHEARS) !== 0){
            return $this->getDropsForCompatibleTool($item);
        }

        $drops = [];
        if(mt_rand(1, 20) === 1)
            $drops[] = $this->getSaplingItem();

        if(mt_rand(1, 20) === 3)
            $drops[] = ItemFactory::get(Item::APPLE);

        return $drops;
    }
}