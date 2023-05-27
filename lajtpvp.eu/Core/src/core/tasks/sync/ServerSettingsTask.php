<?php

declare(strict_types=1);

namespace core\tasks\sync;

use core\Main;
use core\managers\ServerManager;
use core\utils\BroadcastUtil;
use core\utils\Settings;
use core\utils\SoundUtil;
use pocketmine\scheduler\Task;

class ServerSettingsTask extends Task {

    public function onRun() : void {

//        if(Main::getInstance()->getServerManager()->isSettingEnabled(ServerManager::EVENT)) {
//            $dragon = DragonManager::getDragonEgg();
//
//            if((int) date("H") === Settings::EVENT_HOUR_START && (int) date("i") === 0 && (int) date("s") === 0) {
//                if($dragon === null)
//                    DragonManager::start();
//            }
//
//            if(!$dragon && DragonManager::started() && DragonManager::getStartTime() <= time())
//                DragonManager::spawnDragonEgg();
//        }

        if(Main::getInstance()->getServerManager()->isSettingEnabled(ServerManager::TNT)) {
            if(date("H") >= Settings::$TNT_START && date("H") <= Settings::$TNT_END) {
                if(!Settings::$TNTHASENABLED) {
                    Settings::$TNTHASENABLED = true;

                    BroadcastUtil::broadcastCallback(function($onlinePlayer) : void {
                        $onlinePlayer->sendTitle("§l§cTNT", "§7zostalo " . (Settings::$TNTHASENABLED ? "§aWLACZONE" : "§cWYLACZONE"));
                        SoundUtil::addSound([$onlinePlayer], $onlinePlayer->asPosition(), "firework.blast");
                    });
                }
            } else {
                if(Settings::$TNTHASENABLED) {
                    Settings::$TNTHASENABLED = false;

                    BroadcastUtil::broadcastCallback(function($onlinePlayer) : void {
                        $onlinePlayer->sendTitle("§l§cTNT", "§7zostalo " . (Settings::$TNTHASENABLED ? "§aWLACZONE" : "§cWYLACZONE"));
                        SoundUtil::addSound([$onlinePlayer], $onlinePlayer->asPosition(), "firework.blast");
                    });
                }
            }
        }

//        if(DragonManager::started()) {
//            foreach(ScoreboardManager::getScoreboards() as $player => $scoreboard) {
//                if(!$scoreboard instanceof DragonEventScoreboard)
//                    continue;
//
//                if(!$player)
//                    continue;
//
//                $scoreboard->onUpdate();
//            }
//        }
    }
}