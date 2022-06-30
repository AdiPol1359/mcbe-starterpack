<?php

namespace core\manager\managers\hazard;

use core\Main;
use core\manager\BaseManager;
use core\manager\managers\hazard\types\roulette\RouletteGame;

class HazardManager extends BaseManager {

    /** @var BaseHazardGame[] */
    public static array $hazardGames = [];

    public static function init() : void {

        Main::getDb()->query("CREATE TABLE IF NOT EXISTS hazard (name TEXT, players TEXT, time INT)");

        $hazardGames = [
            new RouletteGame()
        ];

        foreach($hazardGames as $hazardGame)
            $hazardGame->init();
    }

    public static function save() : void {
        $hazardGames = [
            new RouletteGame()
        ];

        foreach($hazardGames as $hazardGame)
            $hazardGame->save();
    }

    public static function getHazardGames() : ?array {
        return self::$hazardGames;
    }
}