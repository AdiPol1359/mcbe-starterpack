<?php

namespace Core\api;

use pocketmine\Player;
use pocketmine\math\Vector3;
use Core\Main;

class HomeAPI {

    public static function setHome(Player $player, string $name, Vector3 $pos) : void {
        Main::getInstance()->getDb()->query("INSERT INTO home (nick, name, x, y, z) VALUES ('{$player->getName()}', '{$name}', '{$pos->getX()}', '{$pos->getY()}', '{$pos->getZ()}')");
    }

    public static function deleteHome(Player $player, string $name) : void {
        Main::getInstance()->getDb()->query("DELETE FROM home WHERE nick = '{$player->getName()}' AND name = '$name'");
    }

    public static function isHomeExists(Player $player, string $name) : bool {
        return !empty(Main::getInstance()->getDb()->query("SELECT * FROM home WHERE nick = '{$player->getName()}' AND name = '$name'")->fetchArray());
    }

    public static function getHomePos(Player $player, string $name) : ?Vector3 {
        $array = Main::getInstance()->getDb()->query("SELECT * FROM home WHERE nick = '{$player->getName()}' AND name = '$name'")->fetchArray(SQLITE3_ASSOC);

        if(empty($array))
            return null;

        return new Vector3($array['x'], $array['y'], $array['z']);
    }

    public static function getHomes(Player $player) : array {
        $homes = [];
        $result = Main::getInstance()->getDb()->query("SELECT * FROM home WHERE nick = '{$player->getName()}'");

        while($array = $result->fetchArray(SQLITE3_ASSOC))
            $homes[] = $array['name'];

        return $homes;
    }

    public static function getHomesCount(Player $player) : int {
        return count(self::getHomes($player));
    }

    public static function getMaxHomesCount(Player $player) : int {
        $count = 1;

        if($player->hasPermission("PolishHard.homes.3"))
            $count = 3;

        if($player->hasPermission("PolishHard.homes.5"))
            $count = 5;

        if($player->hasPermission("PolishHard.homes.8"))
            $count = 8;

        return $count;
    }
}