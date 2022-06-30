<?php

namespace core\util\utils;

class TimeUtil {

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

    public static function convertIntToTime(int $time) : string {

        $format = self::getFormat($time);

        return gmdate($format, $time);
    }

    public static function convertIntToStringTime(int $time, string $frontFormat = "§l§9", string $endFormat = "§r§7", bool $short = false, bool $space = true) : string {

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