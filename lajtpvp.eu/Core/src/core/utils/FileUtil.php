<?php

declare(strict_types=1);

namespace core\utils;

use pocketmine\Server;

final class FileUtil {

    private function __construct() {}

    public static function deleteDir($dirPath) : void {
        if(!is_dir($dirPath))
            return;

        if(!str_ends_with($dirPath, '/'))
            $dirPath .= '/';

        $files = glob($dirPath . '*', GLOB_MARK);

        foreach($files as $file) {
            if(is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }

        rmdir($dirPath);
    }

    public static function copyFolder(string $path, string $pathTo) {
        @mkdir($pathTo . basename($path));

        $files = array_diff(scandir($path), [".", ".."]);

        foreach($files as $file) {
            if(is_file($path . $file)) {
                copy($path . $file, $pathTo . '/' . basename($path) . '/' . $file);
            } elseif(is_dir($path . $file)) {
                self::CopyFolder($path . $file . '/', $pathTo . basename($path) . '/');
            }
        }
    }

    public static function removeLevel(string $worldName) : void {
        $server = Server::getInstance();
        $worldManager = $server->getWorldManager();

        if(!$worldManager->isWorldLoaded($worldName))
            return;

        $worldManager->loadWorld($worldName);

        $level = $worldManager->getWorldByName($worldName);

        $path = $level->getProvider()->getPath();

        $worldManager->unloadWorld($level, true);

        self::removeFolder($path);
    }

    public static function removeFolder(string $path) {

        $files = array_diff(scandir($path), [".", ".."]);

        foreach($files as $file) {
            if(is_dir($path . $file)) {
                self::removeFiles($path . $file . "/");
            } elseif(is_file($path . $file)) {
                unlink($path . $file);
            }
        }

        rmdir($path);
    }

    public static function removeFiles(string $path) {

        $files = array_diff(scandir($path), [".", ".."]);

        foreach($files as $file) {
            if(is_dir($path . $file)) {
                self::removeFiles($path . $file . "/");
            } elseif(is_file($path . $file)) {
                unlink($path . $file);
            }
        }

        rmdir($path);
    }

    public static function changeLevelName(string $path, string $newName) {

//        $path .= "level.dat";
//
//        $nbt = new BigEndianNBTStream();
//        $levelData = $nbt->readCompressed(file_get_contents($path));
//        $dataTag = $levelData->getCompoundTag("Data");
//        $dataTag->setString("LevelName", $newName);
//
//        $nbt = new BigEndianNBTStream();
//        $buffer = $nbt->writeCompressed(new CompoundTag("", [
//            $dataTag
//        ]));
//        file_put_contents($path, $buffer);
//
//        Server::getInstance()->loadLevel($newName);
    }
}