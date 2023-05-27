<?php

declare(strict_types=1);

namespace core\items\custom;

use core\utils\MessageUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemIds;

class BoyFarmer extends CustomItem {

    public function __construct() {
        parent::__construct(ItemIds::OBSIDIAN, 0, VanillaBlocks::OBSIDIAN()->asItem()->getName(), "§l§8» §eBOYFARMER §8«", [" ", MessageUtil::format("Postaw aby stworzyc slup z obsydianu!"), MessageUtil::format("Crafting znajdziesz pod §l§8/§ecraftingi§r§7!")]);
    }
}