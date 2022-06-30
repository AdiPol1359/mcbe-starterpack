<?php

namespace core\task\tasks;

use core\caveblock\CaveManager;
use core\Main;
use core\manager\managers\hazard\HazardManager;
use core\manager\managers\market\MarketManager;
use core\manager\managers\terrain\TerrainManager;
use core\user\UserManager;
use core\util\utils\FileUtil;
use pocketmine\scheduler\Task;

class SaveDataTask extends Task{
    public function onRun(int $currentTick) {

        $users = [];

        foreach(UserManager::getUsers() as $user) {
            $user->saveDrop();
            $user->saveSettings();
            $user->saveMoney();
            $user->saveQuests();
            $user->saveSkills();
            $user->saveCobblestone();
            $user->saveServices();
            $user->savePets();
            $user->saveParticles();

            $usr = clone $user;
            $usr->setPos1(null);
            $usr->setPos2(null);

            $users[] = $usr;
        }

        Main::getInstance()->getServer()->getAsyncPool()->submitTask(new MySQLSaveAsyncTask($users, CaveManager::getRegisteredCaves()));

        CaveManager::saveCaves();
        UserManager::saveAllUsers();
        MarketManager::save();
        TerrainManager::saveTerrain();
        HazardManager::save();

        if(!is_dir(Main::getInstance()->getDataFolder()."/data/backup"))
            @mkdir(Main::getInstance()->getDataFolder()."/data/backup");

        $data = gmdate("d.m.Y H.i.s", time());
        if(is_dir(Main::getInstance()->getDataFolder() . 'data/backup/'.$data))
            FileUtil::removeFiles(Main::getInstance()->getDataFolder() . 'data/backup/'.$data);

        FileUtil::copyFolder(Main::getInstance()->getDataFolder()."/data/database/", Main::getInstance()->getDataFolder()."/data/backup/");
        rename(Main::getInstance()->getDataFolder() . 'data/backup/database', Main::getInstance()->getDataFolder() . 'data/backup/'.$data);
    }
}