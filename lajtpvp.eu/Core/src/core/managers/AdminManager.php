<?php

declare(strict_types=1);

namespace core\managers;

use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use core\utils\PermissionUtil;
use core\utils\Settings;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AdminManager {

    public static function sendMessage(CommandSender $player, string $message, bool $selfMessage = false) : void {

        if($player instanceof Player) {
            if(!$player->isConnected())
                return;
        }

        if(!PermissionUtil::has($player, Settings::$PERMISSION_TAG."admin.broadcast"))
            return;

        BroadcastUtil::broadcastAdmins(function($onlinePlayer) use ($player, $selfMessage, $message) : void {
            if(!$selfMessage) {
                if($player->getName() === $onlinePlayer->getName())
                    return;
            }

            $onlinePlayer->sendMessage(MessageUtil::adminFormat($message));
        });
    }
}