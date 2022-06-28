<?php

namespace Core\api;

use pocketmine\Player;
use Core\Main;

class NameTagsAPI {

    public const DEVICE_NONE = 0;
    public const DEVICE_MOBILE = 1;
    public const DEVICE_PC = 2;

    private static $device = [];

    public static function setDevice(Player $player, int $device) : void {
        self::$device[$player->getName()] = $device;
    }

    public static function getDevice(Player $player) : ?int {
        if(isset(self::$device[$player->getName()]))
            return self::$device[$player->getName()];

        return null;
    }
    
    public static function getNameTag(Player $player) : string {
    	   $nick = $player->getName();

        $points = Main::getInstance()->getPointsAPI()->getPoints($nick);

        $cps = CpsAPI::getCPS($player);

        $deviceFormat = "?";

        if(self::getDevice($player) != null) {
            switch (self::getDevice($player)) {
                case self::DEVICE_MOBILE:
                    $deviceFormat = "MOBILE";
                break;

                case self::DEVICE_PC:
                    $deviceFormat = "DESKTOP";
                break;
            }
        }

        $nametag = "{$nick}\n"."§4{$deviceFormat}\n"."§6{$points} §fpkt";
        
        return $nametag;
    }

    public static function getGuildNameTag(Player $player) : string {
        $nick = $player->getName();

        $cps = CpsAPI::getCPS($player);

        $deviceFormat = "?";

        if(self::getDevice($player) != null) {
            switch (self::getDevice($player)) {
                case self::DEVICE_MOBILE:
                    $deviceFormat = "MOBILE";
                    break;

                case self::DEVICE_PC:
                    $deviceFormat = "DESKTOP";
                    break;
            }
        }

        $nametag = "{$nick}\n"."§4{$deviceFormat}";

        return $nametag;
    }

    public static function setNameTag(Player $player) : void {
        if($player->hasPermission("PolishHard.nametag.ignore"))
            return;

        $g_api = $player->getServer()->getPluginManager()->getPlugin("Gildie");

        if($g_api == null)
            return;

        if($g_api->getGuildManager()->isInGuild($player->getName()))
            return;

        $player->setNameTag("§7".self::getNameTag($player));
    }
}