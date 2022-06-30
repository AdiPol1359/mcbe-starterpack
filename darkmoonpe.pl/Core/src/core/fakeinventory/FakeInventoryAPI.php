<?php

namespace core\fakeinventory;

use pocketmine\Player;

class FakeInventoryAPI {

    /** @var FakeInventory[] */
    private static array $inv = [];

    public static function getInventory(string $player) : ?FakeInventory {
        return isset(self::$inv[$player]) ? self::$inv[$player] : null;
    }

    public static function isOpening($player) : bool {
        return $player instanceof Player ? isset(self::$inv[$player->getName()]) : isset(self::$inv[(string) $player]);
    }

    public static function setInventory($player, FakeInventory $inv) : void {
        $player instanceof Player ? self::$inv[$player->getName()] = $inv : self::$inv[(string) $player] = $inv;
    }

    public static function unsetInventory($player) : void {
        if($player instanceof Player)
            unset(self::$inv[$player->getName()]);
        else
            unset(self::$inv[(string) $player]);
    }

    public static function getInventories() : array {
        return self::$inv;
    }
}