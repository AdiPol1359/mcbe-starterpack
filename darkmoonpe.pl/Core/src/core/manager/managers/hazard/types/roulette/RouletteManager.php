<?php

namespace core\manager\managers\hazard\types\roulette;

use core\fakeinventory\inventory\hazard\roulette\RouletteDraw;
use core\manager\BaseManager;
use core\util\utils\ConfigUtil;
use core\util\utils\RandomUtil;
use pocketmine\item\Item;
use pocketmine\Player;

class RouletteManager extends BaseManager {
    
    public static function getRandomItem() : Item {

        $black = Item::get(Item::CONCRETE, 15)->setCustomName("§l§8CZARNY");
        $red = Item::get(Item::CONCRETE, 14)->setCustomName("§l§cCZERWONY");
        $green = Item::get(Item::CONCRETE, 5)->setCustomName("§l§aZIELONY");

        $items["black"] = 50;
        $items["red"] = 50;
        $items["green"] = 10;

        switch(RandomUtil::randomDraw($items)) {
            case "black":
                return $black;

            case "red":
                return $red;

            case "green":
                return $green;
        }

        return self::getRandomItem();
    }

    public static function openRoulette(Player $player) : void {

        if($player->getLevel()->getName() === ConfigUtil::PVP_WORLD || $player->getLevel()->getName() === ConfigUtil::BOSS_WORLD)
            return;

        (new RouletteDraw())->openFor([$player]);
    }
}