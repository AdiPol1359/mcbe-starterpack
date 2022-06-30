<?php

namespace core\task\tasks;

use core\manager\managers\hazard\BaseHazardGame;
use core\manager\managers\hazard\types\roulette\RouletteGame;
use core\manager\managers\hazard\types\roulette\RouletteManager;
use core\manager\managers\ServerManager;
use core\manager\managers\SettingsManager;
use core\manager\managers\SoundManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class RouletteTask extends Task {

    public function onRun(int $currentTick) {

        /**
         * @var string $name
         * @var BaseHazardGame $game
         */

        if(!ServerManager::isSettingEnabled(ServerManager::HAZARD))
            return;

        $time = RouletteGame::getTime();

        if($time <= time()){

            foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer){

                $user = UserManager::getUser($onlinePlayer->getName());

                if(!$user)
                    continue;

                $roulettePlayer = RouletteGame::getPlayer($onlinePlayer->getName());

                if($user->isSettingEnabled(SettingsManager::HAZARD_INFO) || $roulettePlayer !== null) {
                    SoundManager::addSound($onlinePlayer, $onlinePlayer->asPosition(), "mob.cat.meow");
                    $onlinePlayer->sendMessage(MessageUtil::formatLines([RouletteGame::getName() . "§r§7 sie rozpoczela!", "Aby zobaczyc losowanie wpisz §l§8/§9hazard§r§7!"]));
                }
            }

            RouletteGame::setTime(time() + (ConfigUtil::ROULETTE_TIME + 60));

            foreach(RouletteGame::getPlayers() as $nick => $roulettePlayer) {
                $player = Server::getInstance()->getPlayerExact($nick);

                if(!$player)
                    continue;

                RouletteManager::openRoulette($player);
            }

            RouletteDrawTask::getInstance()->start();

            return;
        }

        $message = "";
        $tip = "";

        switch(($rouletteTime = ($time - time()))){
            case 60*60:
            case 60*50:
            case 60*40:
            case 60*30:
            case 60*20:
            case 60*10:
            case 60*5:
                $message = MessageUtil::formatLines(["Za §l§9".($rouletteTime / 60)."§r§7 minut rozpocznie sie §l§9".RouletteGame::getName()."§r§7!", "Aby dolaczyc wpisz §l§8/§9hazard"]);
                break;

            case 60*1:

                RouletteGame::setLock(true);
                $message = MessageUtil::formatLines(["Za §l§9".($rouletteTime / 60)."§r§7 minute rozpocznie sie §l§9Ruletka§r§7!", "Zablokowano betowanie w ruletce§r§7!"]);
                break;

            case 10:

                RouletteGame::setStartGame(true);
                $message = MessageUtil::formatLines(["Za §l§910 §r§7sekund zacznie sie losowanie! Mozna juz otworzyc menu losowania pod §l§8/§9hazard"]);
                break;
        }

        if($rouletteTime <= 20)
            $tip = "§7Ruletka za §l§9".$rouletteTime."§r§7 sekund!";

        foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer){

            if($onlinePlayer->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
                continue;

            $user = UserManager::getUser($onlinePlayer->getName());

            if(!$user)
                continue;

            $roulettePlayer = RouletteGame::getPlayer($onlinePlayer->getName());

            if($user->isSettingEnabled(SettingsManager::HAZARD_INFO) || $roulettePlayer !== null) {

                if($message !== "") {
                    SoundManager::addSound($onlinePlayer, $onlinePlayer->asPosition(), "random.pop", 100, 3);
                    $onlinePlayer->sendMessage($message);
                }

                if($tip !== "") {
                    SoundManager::addSound($onlinePlayer, $onlinePlayer->asPosition(), "random.pop", 100, 3);
                    $onlinePlayer->sendTip($tip);
                }
            }
        }
    }
}