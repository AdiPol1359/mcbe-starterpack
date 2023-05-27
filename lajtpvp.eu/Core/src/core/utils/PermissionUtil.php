<?php

declare(strict_types=1);

namespace core\utils;

use core\Main;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\Server;

final class PermissionUtil {

    public static function has(CommandSender $target, string $permission): bool {
        if ($target->getServer()->isOp($target->getName()) || $target instanceof ConsoleCommandSender) {
            return true;
        }

        if ($target instanceof Player) {
            return in_array($permission, Main::getInstance()->getPlayerGroupManager()->getPlayer($target->getName())->getPermissions());
        }

        $instance = PermissionManager::getInstance();
        $perm = $instance->getPermission($permission);
        if ($perm === null) {
            $instance->addPermission(new Permission($permission, ""));
        }

        return $target->hasPermission($permission);
    }

    public static function hasOfflinePlayer(string $target, string $permission): bool {
        if (Server::getInstance()->isOp($target)) {
            return true;
        }

        if(!($pPermission = Main::getInstance()->getPlayerGroupManager()->getPlayer($target))) {
            return false;
        }

        return isset($pPermission->getAllPermissions()[$permission]);
    }
}