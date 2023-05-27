<?php

declare(strict_types=1);

namespace core\managers;

use core\Main;
use core\tasks\sync\TeleportTask;
use core\utils\Settings;
use pocketmine\world\Position;
use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;

class TeleportManager {

    /** @var TaskHandler[] */
    private static array $teleports = [];

    public static function teleport(Player $player, Position $position) : void {
        //TODO: zmienic funkcje ze statycznej na dynamiczna
        self::$teleports[$player->getName()] = Main::getInstance()->getScheduler()->scheduleRepeatingTask(new TeleportTask($player->getName(), Settings::$TELEPORT, $position), 20);
    }

    public static function isTeleporting(string $playerName) : bool {
        return isset(self::$teleports[$playerName]);
    }

    public static function cancelTeleport(string $playerName) : void {
        unset(self::$teleports[$playerName]);
    }

    public static function getTeleport(string $playerName) : TaskHandler {
        return self::$teleports[$playerName];
    }
}