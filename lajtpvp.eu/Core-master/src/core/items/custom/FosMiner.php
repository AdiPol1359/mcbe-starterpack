<?php

declare(strict_types=1);

namespace core\items\custom;

use core\utils\MessageUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemIds;

class FosMiner extends CustomItem {

    public function __construct() {
        parent::__construct(ItemIds::STONE, 0, VanillaBlocks::STONE()->asItem()->getName(), "§l§8» §eKOPACZ FOS §8«", [" ", MessageUtil::format("Postaw aby zniszczyc wszystkie bloki pod!"), MessageUtil::format("Crafting znajdziesz pod §l§8/§ecraftingi§r§7!")]);
    }
}