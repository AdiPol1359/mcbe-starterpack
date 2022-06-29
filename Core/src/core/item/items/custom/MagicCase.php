<?php

namespace core\item\items\custom;

use pocketmine\block\Block;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;

class MagicCase extends ItemBlock {

    public function __construct(){
        $this->setCustomName("§l§8» §9MagicCase §8«");
        $this->setLore([
            " ",
            "§r§l§8» §r§7Postaw aby zaczac losowanie!",
            "§r§l§8» §r§7Mozna zakupic na stronie internetowej!",
            "§r§l§8» §l§9www.DarkMoonPE.PL"
        ]);
        $this->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10));

        parent::__construct(Block::ENDER_CHEST, 0, Item::ENDER_CHEST);
    }
}