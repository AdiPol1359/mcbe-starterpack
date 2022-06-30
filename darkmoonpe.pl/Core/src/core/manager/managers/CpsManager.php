<?php

namespace core\manager\managers;

use core\manager\BaseManager;
use core\util\utils\MessageUtil;
use pocketmine\Player;

class CpsManager extends BaseManager {

    private static array $data = [];
    public static array $blockAttack = [];
    const CPS_MAX = 12;
    const COOLDOWN = 5;

    public static function setData(Player $player, int $clicks, $lastClick, int $seconds) : void{
        self::$data[$player->getName()] = [$clicks, $lastClick, $seconds];
    }

    public static function setDefaultData(Player $player) : void{
        self::setData($player, 0, time(), 0);
    }

    public static function Click(Player $player) : void{

        $nick = $player->getName();
        $lastClick = time() - self::getLastClick($player);
        $time = self::getSeconds($player);

        if($lastClick <= 0) {
            self::$data[$nick][0] += 1;
            self::$data[$nick][1] = time();

            if ($time < 1)
                self::$data[$nick][2] += 1;

        }elseif($lastClick == 1){
            self::$data[$nick][0] += 1;
            self::$data[$nick][1] = time();
            self::$data[$nick][2] += 1;
        }else
            self::setData($player, 1, time(), 1);

        $cpsCount = self::getCps($player);

        if(!isset(self::$blockAttack[$nick]) && $cpsCount > self::CPS_MAX)
            self::$blockAttack[$nick] = time();

        if(isset(self::$blockAttack[$nick])){
            $cooldown = self::COOLDOWN - (time() - self::$blockAttack[$player->getName()]);
            if($cooldown <= 0){
                unset(self::$blockAttack[$nick]);
                self::setDefaultData($player);
                return;
            }
            $seconds = self::COOLDOWN - (time() - self::$blockAttack[$nick]);
            $player->sendMessage(MessageUtil::format("Przekroczyles limit cps musisz odczekac §l§9".$seconds."§r§7 sekund!"));
        }
    }

    public static function getClicks(Player $player) : int{
        if(!isset(self::$data[$player->getName()]))
            self::setDefaultData($player);
        return self::$data[$player->getName()][0];
    }

    public static function getSeconds(Player $player) : int{
        if(!isset(self::$data[$player->getName()]))
            self::setDefaultData($player);
        return self::$data[$player->getName()][2];
    }

    public static function getLastClick(Player $player){
        if(!isset(self::$data[$player->getName()]))
            self::setDefaultData($player);
        return self::$data[$player->getName()][1];
    }

    public static function getCPS(Player $player) : int{
        if(!isset(self::$data[$player->getName()]))
            self::setDefaultData($player);

        $lastClick = time() - self::getLastClick($player);
        $clicks = self::getClicks($player);
        $time = self::getSeconds($player);

        if($lastClick >= 1)
            return 0;

        if($clicks == 0)
            return 0;

        return (int) round($clicks / $time);
    }
}