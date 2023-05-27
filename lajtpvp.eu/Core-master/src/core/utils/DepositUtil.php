<?php

declare(strict_types=1);

namespace core\utils;

use core\items\custom\ThrownTNT;
use pocketmine\item\VanillaItems;

final class DepositUtil {

    public function __construct() {}

    public static function getDepositData() : array {
        return [
            Settings::$STAT_GOLDEN_APPLES => ["item" => VanillaItems::GOLDEN_APPLE(), "count" => 0, "limit" => Settings::$REFILE_LIMIT, "normalName" => "Refile"],
            Settings::$STAT_ENDER_PEARLS => ["item" => VanillaItems::ENDER_PEARL(), "count" => 0, "limit" => Settings::$PEARL_LIMIT, "normalName" => "Perly"],
            Settings::$STAT_ENCHANTED_APPLES => ["item" => VanillaItems::ENCHANTED_GOLDEN_APPLE(), "count" => 0, "limit" => Settings::$ENCHANTED_LIMIT, "normalName" => "Koxy"],
            Settings::$STAT_SNOWBALLS => ["item" => VanillaItems::SNOWBALL(), "count" => 0, "limit" => Settings::$SNOWBALL_LIMIT, "normalName" => "Sniezki"],
            Settings::$STAT_ARROWS => ["item" => VanillaItems::ARROW(), "count" => 0, "limit" => Settings::$ARROW_LIMIT, "normalName" => "Strzaly"],
            Settings::$STAT_THROWABLE_TNT => ["item" => (new ThrownTNT())->__toItem(), "count" => 0, "limit" => Settings::$THROWABLE_TNT_LIMIT, "normalName" => "Rzucaki"]
        ];
    }
}