<?php

declare(strict_types=1);

namespace core\items\custom;

use core\utils\MessageUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemIds;

class StoneGenerator extends CustomItem {

    public function __construct() {
        parent::__construct(ItemIds::END_STONE, 0, VanillaBlocks::END_STONE()->asItem()->getName(), "§l§8» §eSTONIARKA §8«", [" ", MessageUtil::format("Postaw stone obok aby generator zaczal dzialac!"), MessageUtil::format("Crafting znajdziesz pod §l§8/§ecraftingi§r§7!")]);
    }
}