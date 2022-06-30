<?php

namespace core\util\utils;

use pocketmine\nbt\{
    BigEndianNBTStream,
    tag\CompoundTag};
use pocketmine\Server;

class FileUtil {

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

        if(!$server->isLevelGenerated($worldName))
            return;

        $server->loadLevel($worldName);

        $level = $server->getLevelByName($worldName);

        $path = $level->getProvider()->getPath();

        $server->unloadLevel($level, true);

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

        $path .= "level.dat";

        $nbt = new BigEndianNBTStream();
        $levelData = $nbt->readCompressed(file_get_contents($path));
        $dataTag = $levelData->getCompoundTag("Data");
        $dataTag->setString("LevelName", $newName);

        $nbt = new BigEndianNBTStream();
        $buffer = $nbt->writeCompressed(new CompoundTag("", [
            $dataTag
        ]));
        file_put_contents($path, $buffer);

        Server::getInstance()->loadLevel($newName);
    }
}