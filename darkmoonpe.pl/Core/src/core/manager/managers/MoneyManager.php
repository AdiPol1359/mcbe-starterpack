<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;
use core\util\utils\ConfigUtil;

class MoneyManager extends BaseManager {

    public static function init() : void {
        Main::getDb()->exec("CREATE TABLE IF NOT EXISTS 'money' (nick TEXT PRIMARY KEY COLLATE NOCASE, money FLOAT);");
    }

    public static function registerPlayer(string $nick) : void {
        if(self::exists($nick))
            return;

        $money = ConfigUtil::DEFAULT_MONEY;
        Main::getDb()->query("INSERT INTO 'money' (nick, money) VALUES ('$nick', $money)");
    }

    public static function exists(string $nick) : bool {
        return !empty(Main::getDb()->query("SELECT * FROM 'money' WHERE nick = '$nick'")->fetchArray());
    }
}