<?php

namespace core\listener;

use pocketmine\event\Listener;
use pocketmine\Server;

abstract class BaseListener implements Listener{

    private static Server $server;

    public function __construct() {
        self::$server = Server::getInstance();
    }

    public function getServer() : Server{
        return self::$server;
    }
}