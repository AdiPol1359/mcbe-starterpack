<?php

namespace core\managers\bossbar;

use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;

class BossbarManager {

    private static array $bossbar = [];

    public static function unsetBossbar(Player $player) : void {
        unset(self::$bossbar[$player->getName()]);
    }

    #[Pure] public static function getBossbar(Player $player) : ?Bossbar {
        return self::$bossbar[$player->getName()] ?? null;
    }

    public static function setBossbar(Player $player, Bossbar $bossbar) : void {
        self::$bossbar[$player->getName()] = $bossbar;
    }

    public static function getBossBars() : array {
        return self::$bossbar;
    }
}