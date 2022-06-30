<?php

namespace core\util\utils;

class StringUtil {

    public static function alignTextArray(array $text, int $length = 37) : array {

        $alignText = [];

        foreach($text as $key => $str) {

            if($str === ""){
                $alignText[] = " ";
                continue;
            }

            $times = 0;

            $explodedText = explode("§l", $str);

            foreach($explodedText as $explodeKey => $explodeStr) {
                if(strpos($explodeStr, "§r") !== false) {
                    $result = substr($explodeStr, 0, strpos($explodeStr, "§r"));

                    if($result === "")
                        continue;

                    $times += floor(strlen($result) / 3);
                }else{
                    $result = substr($explodeStr, 0, strlen($explodeStr));

                    if($result === "")
                        continue;

                    $times += floor(strlen($result) / 3);
                }
            }

            $checkString = preg_replace('/(§([a-z, \d]))/', '', $str);

            $lines = ($length - strlen($checkString));

            if($lines > 0 && $lines % 2 === 0 || $lines > 2) {
                if(strlen($checkString) >= ($length - 2)){
                    $alignText[] = " ".$checkString;
                    continue;
                }

                $alignString = str_pad($checkString, $length + (($lines / 2) - 2), ' ', STR_PAD_BOTH);
                $alignString = str_replace($checkString, $str, $alignString);

                $alignText[] = $alignString;
            }else
                $alignText[] = " ".$str;
        }

        return $alignText;
    }

    public static function alignText(string $text, int $length = 37) : array {

        $alignText = [];

        $wrapText = wordwrap($text, $length, "#space#");
        $arrayText = explode("#space#", $wrapText);

        foreach($arrayText as $key => $str) {

            $lines = ($length - strlen($str));

            if($lines > 0 && $lines % 2 === 0 || $lines > 2) {
                if(strlen($str) >= ($length - 2)){
                    $alignText[] = " ".$str;
                    continue;
                }

                $alignText[] = str_pad($str, $length + (($lines / 2) + 1), ' ', STR_PAD_BOTH);
            }else
                $alignText[] = " ".$str;
        }

        return $alignText;
    }

    public static function correctText(string $text, int $length = 65) : string {
        $lines = ($length - strlen($text));
        $resultString = $text;

        if($lines > 0) {
            if($lines % 2 === 0 || $lines > 2)
                $resultString = str_replace("---", str_repeat("-", (($lines / 2) + 3)), $resultString);
        }

        return $resultString;
    }

    public static function lenghtCalculator(string $text) : int {

        $times = 0;

        $explodedText = explode("§l", $text);

        foreach($explodedText as $key => $str) {
            if(strpos($str, "§r") !== false) {

                $result = substr($str, 0, strpos($str, "§r"));

                if($result === "")
                    continue;

                $times += floor(strlen($result) / 3);
            }
        }

        return $times;
    }
}