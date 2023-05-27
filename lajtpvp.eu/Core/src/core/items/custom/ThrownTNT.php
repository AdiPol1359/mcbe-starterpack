<?php

declare(strict_types=1);

namespace core\items\custom;

use core\utils\MessageUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemIds;

class ThrownTNT extends CustomItem {

    public function __construct() {
        parent::__construct(ItemIds::MOB_SPAWNER, 0, VanillaBlocks::MONSTER_SPAWNER()->asItem()->getName(), "§l§8» §eRZUCAK §8«", [" ", MessageUtil::format("Skieruj sie w stone w ktora chcesz rzucic!"), MessageUtil::format("§r§l§8» §r§7Crafting znajdziesz pod §l§8/§ecraftingi§r§7!")]);
    }
}