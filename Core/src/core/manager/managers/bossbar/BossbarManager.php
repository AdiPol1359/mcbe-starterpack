<?php

namespace core\manager\managers\bossbar;

use core\manager\BaseManager;
use pocketmine\Player;

class BossbarManager extends BaseManager {

    private static array $bossbar = [];

    public static function unsetBossbar(Player $player) : void {
        unset(self::$bossbar[$player->getName()]);
    }

    public static function getBossbar(Player $player) : ?Bossbar {
        return self::$bossbar[$player->getName()] ?? null;
    }

    public static function setBossbar(Player $player, Bossbar $bossbar) : void {
        self::$bossbar[$player->getName()] = $bossbar;
    }
}