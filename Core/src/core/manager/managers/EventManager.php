<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;

class EventManager extends BaseManager {

    public static function addPlayer(string $nick) : void {
        $players = Main::getEvent()->get("players");
        if(in_array($nick, $players))
            return;
        $players[] = $nick;
        Main::getEvent()->set("players", $players);
        Main::getEvent()->save();
    }

    public static function removePlayer(string $nick) : void {
        $players = Main::getEvent()->get("players");
        unset($players[array_search($nick, $players)]);

        $newArray = [];

        foreach($players as $player)
            $newArray[] = $player;

        Main::getEvent()->set("players", $newArray);
        Main::getEvent()->save();
    }

    public static function isInEvent(string $nick) : bool {
        $players = self::getEventPlayers();

        if(in_array($nick, $players))
            return true;

        return false;
    }

    public static function getEventPlayers() : array {
        return Main::getEvent()->get("players");
    }
}