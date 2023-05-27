<?php

declare(strict_types=1);

namespace core\items\custom;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;

class FastPickaxe extends CustomItem {

    public function __construct() {
        parent::__construct(ItemIds::DIAMOND_PICKAXE, 0, VanillaItems::DIAMOND_PICKAXE()->getName(), "§r§l§eSZYBKI KILOF 6§8/§e3§8/§e3", [], [new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 6), new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3), new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::FORTUNE), 3)], false);
    }
}