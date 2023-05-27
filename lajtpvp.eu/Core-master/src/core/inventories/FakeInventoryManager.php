<?php

declare(strict_types=1);

namespace core\inventories;

final class FakeInventoryManager {

    /** @var FakeInventory[] */
    private static array $playerInventories = [];
    private static bool $sendPacket = true;

    public static function getInventory(string $nick) : ?FakeInventory {
        return self::$playerInventories[$nick] ?? null;
    }

    public static function isOpening(string $nick) : bool {
        return isset(self::$playerInventories[$nick]);
    }

    public static function setInventory(string $player, FakeInventory $inv) : void {
        self::$playerInventories[$player] = $inv;
    }

    public static function unsetInventory(string $nick) : void {
        unset(self::$playerInventories[$nick]);
    }

    public static function getInventories() : array {
        return self::$playerInventories;
    }

    public static function setSendPacket(bool $value) : void {
        self::$sendPacket = $value;
    }

    public static function hasSendPacket() : bool {
        return self::$sendPacket;
    }
}