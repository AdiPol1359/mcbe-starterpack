<?php

declare(strict_types=1);

namespace core\utils;

use JetBrains\PhpStorm\Pure;

final class TimeUtil {

    private function __construct() {}

    public static function getTimeFromFormat(string $time, bool $addTime) : ?int {
        switch(strtolower($time[strlen($time) - 1])) {
            case "s":
                $time = (int) str_replace('s', '', $time);
                break;
            case "m":
                $time = (int) str_replace('m', '', $time);
                $time = $time * 60;
                break;
            case "h":
                $time = (int) str_replace('h', '', $time);
                $time = $time * 3600;
                break;
            case "d":
                $time = (int) str_replace('d', '', $time);
                $time = $time * 86400;
                break;

            default:
                return null;
        }

        if($addTime)
            $time += time();

        return $time;
    }

    public static function getFormat(int $time) : string {

        $format = "";

        if($time >= 86400)
            $format .= "d ";

        if($time >= 3600)
            $format .= "H:";

        if($time >= 60)
            $format .= "i:";

        return ($format . "s");
    }

    #[Pure] public static function convertIntToTime(int $time) : string {

        $format = self::getFormat($time);

        return date($format, $time);
    }

    #[Pure] public static function convertIntToStringTime(int $time, string $frontFormat = "§l§e", string $endFormat = "§r§7", bool $short = false, bool $space = true) : string {

        if($time < 0)
            $time = 0;

        $seconds = $time % 60;
        $minutes = null;
        $hours = null;
        $days = null;

        if($time >= 60){
            $minutes = floor(($time % 3600) / 60);
            if($time >= 3600){
                $hours = floor(($time % (3600 * 24)) / 3600);
                if($time >= 3600 * 24)
                    $days = floor($time / (3600 * 24));
            }
        }

        return ($minutes !== null ?
                ($hours !== null ?
                    ($days !== null ?
                        $frontFormat.$days.($space ? " " : "").$endFormat.($short ? "d" : "dni").", "
                        : "") . $frontFormat.$hours.($space ? " " : "").$endFormat.($short ? "g" : "godzin").", "
                    : "") . $frontFormat.$minutes.($space ? " " : "").$endFormat.($short ? "m" : "minut").", "
                : "") . $frontFormat.($seconds <= 0 ? "0" : $seconds).($space ? " " : "").$endFormat.($short ? "s" : "sekund");
    }
}