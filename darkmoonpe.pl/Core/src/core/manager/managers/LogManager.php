<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;
use core\task\tasks\LogAsyncTask;
use pocketmine\Player;
use pocketmine\utils\Config;

class LogManager extends BaseManager {

    public const ADMIN_LOG = "adminlog";
    public const MONEY = "money";
    public const ADMIN_MONEY = "amoney";
    public const SHOP = "shop";
    public const BLACK_SMITH = "kowal";
    public const MAGIC_CASE = "magiccase";

    public static function sendLog(Player $player, string $message, string $log) : void{
        new Config(Main::getInstance()->getDataFolder() . 'logs/'.$log.'.txt', Config::ENUM);
        Main::getInstance()->getServer()->getAsyncPool()->submitTask(new LogAsyncTask($player->getName(), $message, Main::getInstance()->getDataFolder()."logs/".$log.".txt"));
    }
}