<?php

namespace core\util\utils;

class MessageUtil {
    public static function format(string $message) : string {
        return "§8§l»§r§7 " . $message;
    }

    public static function adminFormat(string $message) : string {
        return "§l§8[§r§d#§l§8]§r§7 " . $message;
    }

    public static function anticheatFormat(string $message) : string {
        return "§l§8[§4§lAC§l§8]§r§7 " . $message;
    }

    public static function formatLines(array $w) : string {
        return " \n§8          §l§9DarkMoonPE.PL\n\n§r§l§8» §r§7" . implode("§r\n§l§8» §r§7", $w) . "\n ";
    }

    public static function customFormat(array $w, string $title = "DarkMoonPE.PL", string $messageFormat = "§l§8» §r§7") : string {
        return " \n§8          §l§9".$title."\n\n§r".$messageFormat . implode("§r\n" . $messageFormat, $w) . "\n ";
    }
}