<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;

class StatsManager extends BaseManager {

    public const KILLS = "kills";
    public const DEATHS = "deaths";
    public const ASSISTS = "assists";
    public const KOXY = "koxy";
    public const REFY = "refy";
    public const PERLY = "perly";
    public const TIME_PLAYED = "timePlayed";
    public const LAST_PLAYED = "lastPlayed";
    public const KILL_STREAK = "killStreak";

    public static function init() : void {
        Main::getDb()->query("CREATE TABLE IF NOT EXISTS stats (nick TEXT, '".self::KILLS."' INT, '".self::DEATHS."' INT, '".self::ASSISTS."' INT, '".self::KOXY."' INT, '".self::REFY."' INT, '".self::PERLY."' INT, '".self::KILL_STREAK."' INT, '".self::TIME_PLAYED."' INT, '".self::LAST_PLAYED."' INT)");
    }

    public static function exists(string $nick) : bool {
        return !empty(Main::getDb()->query("SELECT nick FROM 'stats' WHERE nick = '$nick'")->fetchArray());
    }

    public static function registerPlayer(string $nick) : void {
        if(self::exists($nick))
            return;

        Main::getDb()->query("INSERT INTO stats (nick, '".self::KILLS."', '".self::DEATHS."', '".self::ASSISTS."', '".self::KOXY."', '".self::REFY."', '".self::PERLY."', '".self::KILL_STREAK."', '".self::TIME_PLAYED."', '".self::LAST_PLAYED."') VALUES ('".$nick."', 0, 0, 0, 0, 0, 0, 0, 0, '".time()."')");
    }
}