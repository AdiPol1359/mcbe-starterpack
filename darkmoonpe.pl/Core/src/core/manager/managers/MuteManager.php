<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;
use pocketmine\Player;

class MuteManager extends BaseManager {

    public static function init() : void {
        Main::getDb()->exec("CREATE TABLE IF NOT EXISTS mute (nick TEXT PRIMARY KEY COLLATE NOCASE, author TEXT, time TEXT, reason TEXT)");
    }

    public static function setMute(string $player, $author, int $time, string $reason) : void {
        $date = date('d.m.Y H:i:s', strtotime(date("H:i:s")) + $time);
        Main::getDb()->query("INSERT INTO mute (nick, author, time, reason) VALUES ('$player', '{$author->getName()}', '$date', '$reason')");
    }

    public static function isMuted(string $player) : bool {
        return !empty(Main::getDb()->query("SELECT * FROM mute WHERE nick = '{$player}'")->fetchArray());
    }

    public static function unMute($nick) {
        Main::getDb()->query("DELETE FROM mute WHERE nick = '$nick'");
    }

    public static function getMutedMessage(Player $player) : array {

        $result = Main::getDb()->query("SELECT * FROM mute WHERE nick = '{$player->getName()}'");
        $array = $result->fetchArray(SQLITE3_ASSOC);

        $time = $array["time"];
        $reason = $array['reason'];
        $author = $array['author'];

        return ["§7Jestes zmutowany", "§7Powod: §9§l{$reason}", "§r§7Wygasa: §9§l{$time}", "§r§7Przez:§l§9 {$author}"];

    }
}