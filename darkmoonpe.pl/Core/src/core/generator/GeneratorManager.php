<?php

namespace core\generator;

use core\generator\generators\CaveGenerator;
use core\generator\generators\VoidGenerator;
use core\Main;
use core\manager\BaseManager;
use core\util\utils\ConfigUtil;
use core\util\utils\FileUtil;
use pocketmine\level\Level;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\level\generator\GeneratorManager as GManager;

class GeneratorManager extends BaseManager {

    public static function init() : void {
        GManager::addGenerator(VoidGenerator::class, "void-level");
        GManager::addGenerator(CaveGenerator::class, "cave-level");
        self::createVoidLevel(ConfigUtil::LOBBY_WORLD);
        self::createVoidLevel(ConfigUtil::BOSS_WORLD);
        self::createVoidLevel(ConfigUtil::PVP_WORLD);

        $level = self::getServer()->getLevelByName(ConfigUtil::LOBBY_WORLD);
        $levelProvider = $level->getProvider();

        $compound = $levelProvider->getLevelData()->getCompoundTag("GameRules");
        $compound->setString("doDaylightCycle", "false");

        $levelProvider->saveLevelData();

        $level->setTime(14000);
    }

    public static function createVoidLevel(string $levelName) : void {
        $server = Server::getInstance();

        if($server->getLevelByName($levelName) == null) {
            $server->generateLevel($levelName, null, VoidGenerator::class);
            $server->loadLevel($levelName);
            $server->getLevelByName($levelName)->setSpawnLocation(new Vector3(1, 1, 1));
            $level = Server::getInstance()->getLevelByName($levelName);
            $levelProvider = $level->getProvider();

            $compound = $levelProvider->getLevelData()->getCompoundTag("GameRules");
            $compound->setString("doDaylightCycle", "false");

            $levelProvider->saveLevelData();

            $level->setTime(14000);
        } else
            $server->loadLevel($levelName);
    }

    public static function createWorld(string $name, string $caveName = "CaveDefault") : ?Level
    {
        FileUtil::copyFolder(Main::getInstance()->getDataFolder() . 'caves/' . $caveName . '/', Server::getInstance()->getDataPath() . 'worlds/');

        rename(Server::getInstance()->getDataPath() . 'worlds/' . $caveName . '/', Server::getInstance()->getDataPath() . 'worlds/' . $name);
        FileUtil::changeLevelName(Server::getInstance()->getDataPath() . 'worlds/' . $name . '/', $name);

        return Server::getInstance()->getLevelByName($name);
    }
}