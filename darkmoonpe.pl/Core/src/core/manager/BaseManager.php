<?php

namespace core\manager;

use pocketmine\Server;

abstract class BaseManager{

    private static Server $server;

    public static function init() : void{
        self::$server = Server::getInstance();
    }

    public static function getServer() : Server{
        return self::$server;
    }
}