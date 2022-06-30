<?php

namespace core\block\blocks;

use pocketmine\block\MonsterSpawner;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;

class MobSpawner extends MonsterSpawner{
    public function getDropsForCompatibleTool(Item $item) : array {

        $items = [];

        if($item->hasEnchantment(Enchantment::SILK_TOUCH))
            $items = [Item::get(Item::MOB_SPAWNER)];

        return $items;
    }
}