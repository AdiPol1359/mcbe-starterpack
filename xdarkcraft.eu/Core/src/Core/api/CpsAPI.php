<?php

declare(strict_types=1);

namespace Core\api;

use pocketmine\Player;
use Core\Main;

class CpsAPI {

    private static $data = [];
    public static $blocks = [];

    public static function getCpsMessage(Player $player) : string {
        $seconds = Main::CPS_COOLDOWN - (time() - self::$blocks[$player->getName()]);
        return "§8§l>§r §7Przekroczyles limit CPS §8(§4".Main::CPS_MAX."§8)§7, poczekaj jeszcze §4{$seconds} §7sekund!";
    }

    public static function setData(Player $player, int $clicks, $lastClick, int $seconds) : void {
        self::$data[$player->getName()] = [$clicks, $lastClick, $seconds];
    }

    public static function setDefaultData(Player $player) : void {
        self::setData($player, 0, time(), 0);
    }

    public static function addClick(Player $player) : void {
        $nick = $player->getName();

        $lastClick = time() - self::getLastClick($player);
        $seconds = self::getSeconds($player);

        if($lastClick <= 0) {
            self::$data[$nick][0] += 1;
            self::$data[$nick][1] = time();

            if($seconds < 1)
                self::$data[$nick][2] += 1;
        } elseif($lastClick == 1) {
            self::$data[$nick][0] += 1;
            self::$data[$nick][1] = time();
            self::$data[$nick][2] += 1;
        } else
            self::setData($player, 1, time(), 1);

        $cps = self::getCPS($player);

        if(!isset(self::$blocks[$nick]) && $cps >= Main::CPS_MAX)
            self::$blocks[$nick] = time();

        if(isset(self::$blocks[$nick])) {
            $seconds = Main::CPS_COOLDOWN - (time() - self::$blocks[$player->getName()]);
            if($seconds <= 0) {
                unset(self::$blocks[$nick]);
                self::setDefaultData($player);
                return;
            }

            $player->sendMessage(self::getCpsMessage($player));
        }
    }

    public static function getClicks(Player $player) : int {
    	if(!isset(self::$data[$player->getName()]))
    	 self::setDefaultData($player);
        return self::$data[$player->getName()][0];
    }

    public static function getSeconds(Player $player) : int {
    	if(!isset(self::$data[$player->getName()]))
    	 self::setDefaultData($player);
        return self::$data[$player->getName()][2];
    }

    public static function getLastClick(Player $player) {
    	if(!isset(self::$data[$player->getName()]))
    	 self::setDefaultData($player);
    	
        return self::$data[$player->getName()][1];
    }

    public static function getCPS(Player $player) : int {
        if(!isset(self::$data[$player->getName()]))
            self::setDefaultData($player);

        $clicks = self::getClicks($player);
        $seconds = self::getSeconds($player);
        $lastClick = time() - self::getLastClick($player);

        if($lastClick >= 1)
            return 0;

        if($clicks == 0)
            return 0;

        return (int) round($clicks / $seconds);
    }
}