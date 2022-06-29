<?php

namespace core\manager\managers;

use core\Main;

use core\manager\BaseManager;
use pocketmine\{
    Player
};

class BanManager extends BaseManager {

    public static function init() : void {
        Main::getDb()->exec("CREATE TABLE IF NOT EXISTS ban (nick TEXT PRIMARY KEY COLLATE NOCASE, author TEXT, time TEXT, reason TEXT)");
    }

    public static function setBan(string $player, $author, int $time, string $reason) : void {
        $date = date('d.m.Y H:i:s', strtotime(date("H:i:s")) + $time);
        Main::getDb()->query("INSERT INTO ban (nick, author, time, reason) VALUES ('$player', '{$author->getName()}', '$date', '$reason')");
    }

    public static function isBanned(string $player) : bool {
        return !empty(Main::getDb()->query("SELECT * FROM ban WHERE nick = '{$player}'")->fetchArray());
    }

    public static function unBan(string $player) : void {
        Main::getDb()->query("DELETE FROM ban WHERE nick = '$player'");

        $pl = self::getServer()->getPlayer($player);

        if($pl != null && $pl instanceof Player) {
            $pl->teleport(self::getServer()->getDefaultLevel()->getSafeSpawn());
            foreach(self::getServer()->getOnlinePlayers() as $p) {
                $pl->showPlayer($p);
                $p->showPlayer($pl);
            }
        }

    }

    public static function getBannedMessage(Player $player) : array {

        $nick = $player->getName();

        $result = Main::getDb()->query("SELECT * FROM ban WHERE nick = '$nick'");
        $array = $result->fetchArray(SQLITE3_ASSOC);

        $time = $array["time"];
        $reason = $array['reason'];
        $author = $array['author'];

        return ["§7Powod: §9§l{$reason}", "§r§7Wygasa: §9§l{$time}", "§r§7Przez:§l§9 {$author}"];
    }
}