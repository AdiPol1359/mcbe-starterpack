<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;

class SettingsManager extends BaseManager {

    // INFORMACJE

    public const SCOREBOARD = "scoreboard";
    public const FULL_EQ = "full_eq";
    public const BOT_NOTIFICATION = "bot_notify";
    public const TRADE_REQUEST = "trade_request";
    public const QUEST_BOSSBAR = "quest_bossbar";
    public const HAZARD_INFO = "hazard";

    // ULATWIENIA

    public const AUTO_SPRINT = "autosprint";
    public const NIGHT_VISION = "night_vision";
    public const ITEM_STATUS = "item_status";
    public const COORDINATES = "coordinates";

    // OPTYMALIZACJA

    public const BLOCK_PARTICLE = "block_particle";
    public const PARTICLES = "particle";
    public const SOUNDS = "sounds";
    public const PLAYER_PARTICLES = "player_particle";

    public static function init() : void {
        Main::getDb()->exec("CREATE TABLE IF NOT EXISTS 'settings' (nick TEXT PRIMARY KEY COLLATE NOCASE, 
        ".self::SCOREBOARD." INT,
        ".self::FULL_EQ." INT,
        ".self::BOT_NOTIFICATION." INT,
        ".self::TRADE_REQUEST." INT,
        ".self::QUEST_BOSSBAR." INT,
        ".self::AUTO_SPRINT." INT,
        ".self::NIGHT_VISION." INT,
        ".self::ITEM_STATUS." INT,
        ".self::COORDINATES." INT,
        ".self::BLOCK_PARTICLE." INT,
        ".self::PARTICLES." INT,
        ".self::SOUNDS." INT,
        ".self::PLAYER_PARTICLES." INT,
        ".self::HAZARD_INFO." INT)");
    }

    // setting: scoreboard:1;full_eq:1
    public static function exists(string $nick) : bool {
        return !empty(Main::getDb()->query("SELECT nick FROM 'settings' WHERE nick = '$nick'")->fetchArray());
    }

    public static function registerPlayer(string $nick) : void {
        if(self::exists($nick))
            return;

        Main::getDb()->query("INSERT INTO 'settings' 
        (nick, ".self::SCOREBOARD.", ".self::FULL_EQ.", ".self::BOT_NOTIFICATION.", ".self::TRADE_REQUEST.", ".self::QUEST_BOSSBAR.", ".self::AUTO_SPRINT.", ".self::NIGHT_VISION.", ".self::ITEM_STATUS.", ".self::COORDINATES.", ".self::BLOCK_PARTICLE.", ".self::PARTICLES.", ".self::SOUNDS.", ".self::PLAYER_PARTICLES.", ".self::HAZARD_INFO.")
        VALUES ('$nick', 1, 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1, 1, 1)");
    }
}