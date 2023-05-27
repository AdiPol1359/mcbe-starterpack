<?php

declare(strict_types=1);

namespace core\items\custom;

use core\utils\MessageUtil;
use pocketmine\block\VanillaBlocks;

class CobbleX extends CustomItem {

    public function __construct() {
        parent::__construct(VanillaBlocks::MOSSY_COBBLESTONE()->getId(), 1, VanillaBlocks::MOSSY_COBBLESTONE()->asItem()->getName(), "§l§8» §eCOBBLEX §8«", [" ", MessageUtil::format("Postaw aby otrzymac nagrode!"), MessageUtil::format("Crafting znajdziesz pod §l§8/§ecraftingi§r§7!")]);
    }
}