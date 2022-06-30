<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;

class ServerManager extends BaseManager {

    public const ITEMSHOP = "itemshop";
    public const HAZARD = "hazard";
    public const SHOP = "shop";

    private static array $settings = [];

    public static function init() : void {
        Main::getDb()->query("CREATE TABLE IF NOT EXISTS server (settings TEXT)");
    }

    public static function setDefaultSettings() : void {
        $settings = [
            self::ITEMSHOP => false,
            self::HAZARD => false,
            self::SHOP => true
        ];

        empty(Main::getDb()->query("SELECT * FROM server")->fetchArray(SQLITE3_ASSOC)) ? Main::getDb()->query("INSERT INTO server (settings) VALUES ('".json_encode($settings)."')") : Main::getDb()->query("UPDATE server SET settings = '".json_encode($settings)."'");
    }
    public static function loadSettings() : void {

        if(empty(Main::getDb()->query("SELECT * FROM server")->fetchArray(SQLITE3_ASSOC)["settings"]))
            self::setDefaultSettings();

        $settings = json_decode(Main::getDb()->query("SELECT * FROM server")->fetchArray(SQLITE3_ASSOC)["settings"], true);

        foreach($settings as $setting => $bool)
            self::$settings[$setting] = $bool;
    }

    public static function saveSettings() : void {
        Main::getDb()->query("UPDATE server SET settings = '".json_encode(self::$settings)."'");
    }

    public static function isSettingEnabled(string $name) : bool {
        return self::$settings[$name] ?? false;
    }

    public static function setSetting(string $name, bool $status) : void {
        self::$settings[$name] = $status;
    }

    public static function notify(string $name) : void {
        foreach(self::getServer()->getOnlinePlayers() as $onlinePlayer) {
            $onlinePlayer->addTitle("§9§l" . strtoupper($name), "§7zostal " . (self::isSettingEnabled($name) ? "§9WLACZONY" : "§9WYLACZONY"));
            SoundManager::addSound($onlinePlayer, $onlinePlayer->asPosition(), "firework.blast");
        }
    }
}