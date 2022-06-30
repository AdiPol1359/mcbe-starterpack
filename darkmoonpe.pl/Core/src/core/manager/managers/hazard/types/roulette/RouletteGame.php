<?php

namespace core\manager\managers\hazard\types\roulette;

use core\Main;
use core\manager\managers\hazard\BaseHazardGame;
use core\manager\managers\hazard\HazardManager;

class RouletteGame implements BaseHazardGame {

    /** @var RoulettePlayer[] */
    private static array $players = [];

    private static int $time = 0;
    private static bool $startGame = false;
    private static bool $lock = false;

    public static function init() : void {

        if(empty(Main::getDb()->query("SELECT * FROM hazard WHERE name = '".self::getName()."'")->fetchArray()))
            Main::getDb()->query("INSERT INTO hazard (name, players, time) VALUES ('".self::getName()."', '[]', 0)");

        $query = Main::getDb()->query("SELECT * FROM hazard WHERE name = '".self::getName()."'");

        while($row = $query->fetchArray(SQLITE3_ASSOC)) {
            foreach(json_decode($row["players"], true) as $nick => $bets)
                self::$players[$nick] = new RoulettePlayer($nick, $bets);
        }

        self::setTime(Main::getDb()->query("SELECT * FROM hazard WHERE name = '".self::getName()."'")->fetchArray(SQLITE3_ASSOC)["time"]);
        if(self::$time <= 60*1)
            self::$lock = true;

        HazardManager::$hazardGames[self::getName()] = new RouletteGame();
    }

    public static function save() : void {

        if(empty(Main::getDb()->query("SELECT * FROM hazard WHERE name = '".self::getName()."'")->fetchArray()))
            Main::getDb()->query("INSERT INTO hazard (name, players, time) VALUES ('".self::getName()."', '', 0)");

        $players = [];

        foreach(self::$players as $nick => $roulettePlayer)
            $players[$nick] = $roulettePlayer->getBetAmounts();

        Main::getDb()->query("UPDATE hazard SET players = '".json_encode($players)."', time = '".self::getTime()."' WHERE name = '".self::getName()."'");
    }

    public static function getName() : string {
        return "Ruletka";
    }

    public static function getTime() : int {
        return self::$time;
    }

    public static function setTime(int $time) : void {
        self::$time = $time;
    }

    public static function hasGameStarted() : bool {
        return self::$startGame;
    }

    public static function setStartGame(bool $start) : void {
        self::$startGame = $start;
    }

    public static function isLocked() : bool {
        return self::$lock;
    }

    public static function setLock(bool $lockStatus) : void {
        self::$lock = $lockStatus;
    }

    public static function getPlayers() : array {
        return self::$players;
    }

    public static function getPlayer(string $nick) : ?RoulettePlayer {
        return self::$players[$nick] ?? null;
    }

    public static function createRoulettePlayer(string $nick) : void {
        self::$players[$nick] = new RoulettePlayer($nick, []);
    }

    public static function hasBet(string $nick) : bool {
        return isset(self::$players[$nick]);
    }

    public static function removePlayer(string $nick) : void {
        if(self::hasBet($nick))
            unset(self::$players[$nick]);
    }
}