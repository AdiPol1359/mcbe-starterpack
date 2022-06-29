<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;
use pocketmine\level\Position;
use pocketmine\Player;

class WarpManager extends BaseManager {

    public static function init() : void {
        Main::getDb()->exec("CREATE TABLE IF NOT EXISTS warps (name TEXT PRIMARY KEY COLLATE NOCASE, x DOUBLE, y DOUBLE, z DOUBLE, world DOUBLE)");
    }

    public static function setWarp(string $name, Player $player) : void {
        $x = $player->getX();
        $y = $player->getY();
        $z = $player->getZ();
        $world = $player->getLevel()->getName();

        Main::getDb()->query("INSERT INTO warps (name, x, y, z, world) VALUES ('$name', '$x', '$y', '$z', '$world')");
    }

    public static function removeWarp(string $name) : void {
        Main::getDb()->query("DELETE FROM warps WHERE name = '$name'");
    }

    public static function getWarpPosition(string $name) : Position {
        $array = Main::getDb()->query("SELECT * FROM warps WHERE name = '$name'")->fetchArray(SQLITE3_ASSOC);
        $level = self::getServer()->getLevelByName($array['world']);

        var_dump($level->getName());
        return new Position($array['x'], $array['y'], $array['z'], $level);
    }

    public static function isWarpExists(string $name) : bool {
        $array = Main::getDb()->query("SELECT * FROM warps WHERE name = '$name'")->fetchArray();

        return !empty($array);
    }

    public static function getWarpByIndex(int $index) : ?string {
        $i = 0;

        $array = Main::getDb()->query("SELECT * FROM warps");

        while($row = $array->fetchArray(SQLITE3_ASSOC)) {
            if($i == $index)
                return $row['name'];

            $i++;
        }

        return null;
    }
}