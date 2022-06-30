<?php

namespace core\item\items\custom\fragment;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;

abstract class Fragment extends Item {

    private string $customName;
    private array $lore;

    public function __construct(string $customName = "Fragment", array $lore = []){

        $this->setCustomName($customName);
        $this->setLore($lore);
        $this->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10));

        parent::__construct(self::IRON_NUGGET);
    }

    public function getCustomName() : string {
        return $this->customName;
    }

    public function getLore() : array {
        return $this->lore;
    }
}