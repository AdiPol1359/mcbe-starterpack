<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;

class WhitelistManager extends BaseManager {

    public static function getWhitelistPlayers() : array {
        return Main::getWhitelist()->get("players");
    }

    public static function setWhitelist(bool $status = true) : void {
        Main::getWhitelist()->set("status", $status);
        if(!$status)
            Main::getWhitelist()->set("date", null);
        Main::getWhitelist()->save();
    }

    public static function setWhitelistDate(?string $date = null) : void {
        Main::getWhitelist()->set("date", $date);
        Main::getWhitelist()->save();
    }

    public static function getWhitelistDate() : ?string {
        $date = Main::getWhitelist()->get("date");
        if(!$date)
            return null;
        return $date;
    }

    public static function isWhitelistEnabled() : bool {
        return Main::getWhitelist()->get("status");
    }

    public static function addPlayer(string $nick) : void {
        $nick = strtolower($nick);
        $players = Main::getWhitelist()->get("players");
        if(in_array($nick, $players))
            return;
        $players[] = $nick;
        Main::getWhitelist()->set("players", $players);
        Main::getWhitelist()->save();
    }

    public static function removePlayer(string $nick) : void {
        $nick = strtolower($nick);
        $players = Main::getWhitelist()->get("players");
        unset($players[array_search($nick, $players)]);

        $newArray = [];

        foreach($players as $player)
            $newArray[] = $player;

        Main::getWhitelist()->set("players", $newArray);
        Main::getWhitelist()->save();
    }

    public static function isInWhitelist(string $nick) : bool {
        $nick = strtolower($nick);
        $players = self::getWhitelistPlayers();

        if(in_array($nick, $players))
            return true;

        return false;
    }

    public static function dateFormat() : string {
        $date = self::getWhitelistDate();

        if($date == null)
            return "§7§lComing Soon...";

        $time = strtotime($date) - time();

        $days = intval(intval($time) / (3600 * 24));
        $hours = (intval($time) / 3600) % 24;
        $minutes = (intval($time) / 60) % 60;
        $seconds = intval($time) % 60;

        if($days < 10)
            $days = "0" . $days;

        if($hours < 10)
            $hours = "0" . $hours;

        if($minutes < 10)
            $minutes = "0" . $minutes;

        if($seconds < 10)
            $seconds = "0" . $seconds;

        return "§9{$days} §7dni §9{$hours} §7godzin §9{$minutes} §7minut §9{$seconds} §7sekund";
    }
}