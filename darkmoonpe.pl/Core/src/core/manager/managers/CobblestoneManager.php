<?php

namespace core\manager\managers;

use core\Main;

class CobblestoneManager{
    public static function init() : void {
        Main::getDb()->exec("CREATE TABLE IF NOT EXISTS 'cobblestone' (nick TEXT PRIMARY KEY COLLATE NOCASE, count INT);");
    }

    public static function exists(string $nick) : bool {
        return !empty(Main::getDb()->query("SELECT nick FROM 'cobblestone' WHERE nick = '$nick'")->fetchArray());
    }

    public static function registerPlayer(string $nick) : void {
        if(self::exists($nick))
            return;

        Main::getDb()->query("INSERT INTO 'cobblestone' (nick, count) VALUES ('$nick', 0)");
    }
}