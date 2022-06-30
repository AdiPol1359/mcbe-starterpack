<?php

namespace core\manager\managers;

use core\manager\BaseManager;
use pocketmine\item\Item;

class StatTrackManager extends BaseManager {

    public static function hasStatTrack(Item $item) : bool{
        return $item->getNamedTag()->hasTag("stattrack");
    }

    public static function addStatTrack(Item $item) : void{
        if(self::hasStatTrack($item))
            return;

        $item->getNamedTag()->setInt("stattrack", 0);
        $item->setLore(self::statTrackFormat($item));
    }

    public static function getStatTrack(Item $item) : int{
        if(!self::hasStatTrack($item))
            return 0;

        return $item->getNamedTag()->getInt("stattrack");
    }

    public static function resetStatTrack(Item $item) : void{
        if(!self::hasStatTrack($item))
            return;

        $item->getNamedTag()->setInt("stattrack", 0);
        $item->setLore(self::statTrackFormat($item));
    }

    public static function addToStatTrack(Item $item, int $count = 1) : void{
        if(!self::hasStatTrack($item))
            return;

        $item->getNamedTag()->setInt("stattrack", $item->getNamedTag()->getInt("stattrack") + $count);
        $item->setLore(self::statTrackFormat($item));
    }

    public static function statTrackFormat(Item $item) : array{
        return [
            " ",
            " ",
            "§l§8§k1§r§l§9STATTRACK§l§8§k1§r",
            "§l§8» §r§7Wykopane bloki: §l§9".self::getStatTrack($item)."§r",
            "§l§8» §r§7StatTrack mozna zresetowac u kowala!"
        ];
    }
}