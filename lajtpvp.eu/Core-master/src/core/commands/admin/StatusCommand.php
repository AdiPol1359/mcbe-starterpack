<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use core\utils\TimeUtil;
use pocketmine\command\CommandSender;
use pocketmine\utils\Process;

class StatusCommand extends BaseCommand{

    public function __construct(){
        parent::__construct("status", "", true, true);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        $server = $sender->getServer();
        $bandwidth = $server->getNetwork()->getBandwidthTracker();

        $message = [];

        $time = (int) (microtime(true) - $server->getStartTime());

        $uptime = TimeUtil::convertIntToStringTime($time, "§e");

        $message[] = "Uptime§8: ".$uptime;

        $message[] = "Aktualne TPSy§8: §e".$server->getTicksPerSecond()."§r§8 (§e".$sender->getServer()->getTickUsage()."§r§7%§8)";
        $message[] = "Srednie TPSy§8: §e".$server->getTicksPerSecondAverage()."§r§8 (§e".$sender->getServer()->getTickUsageAverage()."§r§7%§8)";

        $message[] = "Network upload§8: §e".number_format($bandwidth->getSend()->getAverageBytes() / 1024, 2, ".", "")."§r§7 kB/s";
        $message[] = "Network download§8: §e".number_format($bandwidth->getReceive()->getAverageBytes() / 1024, 2, ".", "")."§r§7 kB/s";

        $message[] = "Ilosc watkow§8: §e".Process::getThreadCount();

        $sender->sendMessage(MessageUtil::formatLines($message));

        $message = "";
        $levelsCount = 0;

        $levels = [];

        foreach($sender->getServer()->getWorldManager()->getWorlds() as $level)
            $levels[$level->getDisplayName()] = round($level->getTickRateTime(), 2);

        arsort($levels);

        foreach($levels as $levelName => $tickRate) {

            if($levelsCount >= 30)
                break;

            $level = $sender->getServer()->getWorldManager()->getWorldByName($levelName);

            if(!$level)
                continue;

            $chunks = number_format(count($level->getLoadedChunks()));
            $entities = number_format(count($level->getEntities()));

            $message .= "§7".$levelName."§r§8: §e".$chunks." §r§7chunki, §e".$entities."§r§7 entities, §r§7Opoznienie§8 §e".$tickRate."§r§7ms\n";
            $levelsCount++;
        }

        $sender->sendMessage($message);
    }
}