<?php

namespace core\command;

use core\Main;
use core\manager\BaseManager;

class CommandManager extends BaseManager{

    public static array $commands;

    public static function registerCommands() : void {

        foreach(scandir(__DIR__ . "/commands") as $files) {
            if(!strpos($files, ".php"))
                continue;

            $fileName = __NAMESPACE__ . '\commands\\' . str_replace(".php", "", $files);
            $class = new $fileName;
            self::$commands[] = $class;
        }

        self::getServer()->getCommandMap()->registerAll("core", self::$commands);
    }

    public static function unregisterCommands() : void {

        $commands = [
            "list",
            "ban",
            "ban-ip",
            "pardon",
            "pardon-ip",
            "gamemode",
            "msg",
            "me",
            "checkperm",
            "suicide",
            "help",
            "?",
            "clear",
            "say",
            "reload",
            "whitelist",
            "mixer",
            "version",
            "banlist",
            "playsound",
            "seed",
            "stopsound",
            "title",
            "transferserver",
            "dumpmemory",
            "enchant",
            "particle",
            "status"
        ];

        foreach($commands as $cmdName) {
            $cmd = self::getServer()->getCommandMap()->getCommand($cmdName);

            if($cmd != null)
                self::getServer()->getCommandMap()->unregister($cmd);
        }
    }
}