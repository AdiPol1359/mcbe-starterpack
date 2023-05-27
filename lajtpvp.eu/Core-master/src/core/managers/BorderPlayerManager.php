<?php

declare(strict_types=1);

namespace core\managers;

use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;

class BorderPlayerManager {

    /** @var Player[] */
    private static array $players = [];

    public static function addPlayer(Player $player) : void {
        self::$players[] = $player;
    }

    public static function removePlayer(string $nick) : void {
        foreach(self::$players as $key => $player) {
            if($player->getName() === $nick)
                unset(self::$players[$key]);
        }
    }

    #[Pure] public static function isInBorder(string $nick) : bool {
        foreach(self::$players as $key => $player) {
            if($player->getName() === $nick)
                return true;
        }

        return false;
    }

    public static function getPlayers() : array {
        return self::$players;
    }
}