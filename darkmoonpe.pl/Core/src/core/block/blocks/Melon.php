<?php

namespace core\block\blocks;

use pocketmine\block\Melon as PMMelon;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class Melon extends PMMelon {

    public function getDropsForCompatibleTool(Item $item) : array{
        return [
            ItemFactory::get(Item::MELON_SLICE, 0, mt_rand(1, 4))
        ];
    }
}