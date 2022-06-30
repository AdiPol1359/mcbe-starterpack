<?php

namespace core\item\items\custom;

use pocketmine\block\Block;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;

class Cobblex extends ItemBlock{

    public function __construct(){

        $this->setCustomName("§l§8» §9Cobblex §8«");
        $this->setLore([
            " ",
            "§r§l§8» §r§7Postaw aby zaczac losowanie!",
            "§r§l§8» §r§7Zakupiony przez komende §l§8/§9cobblex§r§7!"
        ]);

        $this->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10));

        parent::__construct(Block::MOSSY_COBBLESTONE, 0, Item::MOSSY_COBBLESTONE);
    }
}