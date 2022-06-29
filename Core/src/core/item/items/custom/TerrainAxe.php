<?php

namespace core\item\items\custom;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;

class TerrainAxe extends Item{

    public function __construct(){

        $this->setCustomName("§l§8» §9Siekiera §8«");
        $this->setLore([
            " ",
            "§r§l§8» §r§7Kliknij lewym przciskiem aby zaznaczyc pierwsza pozycje!",
            "§r§l§8» §r§7Kliknij prawym przciskiem aby zaznaczyc druga pozycje!"
        ]);

        $this->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10));

        parent::__construct(Item::WOODEN_AXE);
    }
}