<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\MessageUtil;
use core\util\utils\TimeUtil;
use pocketmine\command\CommandSender;
use pocketmine\utils\Process;
use const pocketmine\START_TIME;

class StatusCommand extends BaseCommand{

    public function __construct(){
        parent::__construct("status", "Status Command", true, true, "Komenda about sluzy do wyswietlania obciazenia serwery itd.");
    }

    public function onCommand(CommandSender $player, array $args) : void {

        $message = [];

        $time = (int) (microtime(true) - START_TIME);

        $uptime = TimeUtil::convertIntToStringTime($time);

        $message[] = "Uptime§8: ".$uptime;

        $message[] = "Aktualne TPSy§8: §l§9".$this->getServer()->getTicksPerSecond()."§r§8 (§9§l".$this->getServer()->getTickUsage()."§r§7%§8)";
        $message[] = "Srednie TPSy§8: §l§9".$this->getServer()->getTicksPerSecondAverage()."§r§8 (§9§l".$this->getServer()->getTickUsageAverage()."§r§7%§8)";

        $message[] = "Network upload§8: §l§9".number_format($this->getServer()->getNetwork()->getUpload(), 2, ".", "")."§r§7 kB/s";
        $message[] = "Network download§8: §l§9".number_format($this->getServer()->getNetwork()->getDownload(), 2, ".", "")."§r§7 kB/s";

        $message[] = "Ilosc rdzeniow§8: §l§9".Process::getThreadCount();

        $player->sendMessage(MessageUtil::formatLines($message));

        $message = "";
        $levelsCount = 0;

        $levels = [];

        foreach($this->getServer()->getLevels() as $level)
            $levels[$level->getName()] = round($level->getTickRateTime(), 2);

        arsort($levels);

        foreach($levels as $levelName => $tickRate) {

            if($levelsCount >= 30)
                break;

            $level = $this->getServer()->getLevelByName($levelName);

            if(!$level)
                continue;

            $chunks = number_format(count($level->getChunks()));
            $entities = number_format(count($level->getEntities()));

            $message .= "§7".$levelName."§r§8: §l§9".$chunks." §r§7chunki, §l§9".$entities."§r§7 entities, §r§7Opoznienie§8 §l§9".$tickRate."§r§7ms\n";
            $levelsCount++;
        }

        $player->sendMessage($message);
    }
}