<?php

namespace core\item;

use core\item\enchantments\KnockbackEnchantment;
use core\manager\BaseManager;
use pocketmine\item\enchantment\Enchantment;

class EnchantManager extends BaseManager {

    public static function init() : void {
        Enchantment::registerEnchantment(new KnockbackEnchantment(Enchantment::KNOCKBACK, "%enchantment.knockback", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_SWORD, Enchantment::SLOT_NONE, 2));
    }
}