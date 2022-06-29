<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;

class DropManager extends BaseManager {

    public static function init() : void {
        Main::getDb()->exec("CREATE TABLE IF NOT EXISTS 'drop' (nick TEXT PRIMARY KEY COLLATE NOCASE, cobble INT, money INT, emerald INT, diamond INT, gold INT, iron INT, coal INT);");
    }

    public static function exists(string $nick) : bool {
        return !empty(Main::getDb()->query("SELECT nick FROM 'drop' WHERE nick = '$nick'")->fetchArray());
    }

    public static function registerPlayer(string $nick) : void {
        if(self::exists($nick))
            return;

        Main::getDb()->query("INSERT INTO 'drop' (nick, cobble, money, emerald, diamond, gold, iron, coal) VALUES ('$nick', 1, 1, 1, 1, 1, 1, 1)");
    }
}