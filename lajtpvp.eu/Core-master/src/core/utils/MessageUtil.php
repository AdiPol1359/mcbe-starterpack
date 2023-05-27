<?php

declare(strict_types=1);

namespace core\utils;

use JetBrains\PhpStorm\Pure;

final class MessageUtil {

    public function __construct() {}

    public static function fixColor(string $str) : array|string {
        return str_replace("&", "§", $str);
    }

    public static function fixColors(array $str) : array|string {
        $result = "";

        foreach($str as $key => $string) {
            $result .= str_replace("&", "§", $string) . "\n";
        }

        return $result;
    }

    #[Pure] public static function formatLines(array $w, string|null $title = null) : string {
        //§8[§7========§8[ §l§eLajtPVP.PL§r§8 ]§7========§8]
        return " \n§8<§7=========§8[§l§e " . ($title === null ? Settings::$SERVER_NAME : $title) . " §r§8]§7=========§8>\n§r§8» §r§7" . implode("§r\n§8» §r§7", $w) . "\n ";
    }

    public static function format(string $str) : string {
        return "§8» §r§7" . str_replace("&", "§", $str); // zostawic tutaj kolor 7
    }

    public static function adminFormat(string $message) : string {
        return "§l§8[§r§e#§l§8]§r§7 " . $message;
    }

    public static function anticheatFormat(string $message) : string {
        return "§l§8[§4§lAC§l§8]§r§7 " . $message;
    }
}