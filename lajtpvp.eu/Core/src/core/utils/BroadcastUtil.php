<?php

declare(strict_types=1);

namespace core\utils;

use pocketmine\Server;

final class BroadcastUtil {

    private function __construct() {}

    public static function broadcastCallback(callable $callback) : void {
        foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            $callback($onlinePlayer);
        }
    }

    public static function broadcastAdmins(callable $callback) : void {
        foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if((!PermissionUtil::has($onlinePlayer, Settings::$PERMISSION_TAG."broadcast.admin") && !$onlinePlayer->getServer()->isOp($onlinePlayer->getName()))) {
                continue;
            }

            $callback($onlinePlayer);
        }
    }
}