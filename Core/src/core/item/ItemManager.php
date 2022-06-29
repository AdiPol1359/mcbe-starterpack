<?php

namespace core\item;

use core\item\items\custom\Cobblex;
use core\item\items\custom\fragment\fragments\UpgradeFragment;
use core\item\items\custom\MagicCase;
use core\item\items\custom\TerrainAxe;
use core\item\items\EnderPearl;
use core\item\items\Fireworks;
use core\item\items\GoldenApple;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class ItemManager {

    public static function init() : void {

        ItemFactory::registerItem(new EnderPearl(), true);
        ItemFactory::registerItem(new Fireworks(), true);
        ItemFactory::registerItem(new GoldenApple(), true);

        $creativeItems = [
            new Cobblex(),
            new MagicCase(),
            new UpgradeFragment(),
            new TerrainAxe()
        ];

        foreach($creativeItems as $item)
            Item::addCreativeItem($item);
    }
}