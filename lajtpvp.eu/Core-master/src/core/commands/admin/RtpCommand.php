<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;

use core\Main;
use core\managers\AdminManager;
use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class RtpCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("rtp", "", true, true);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $players = [];
        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

        if(!$user) {
            return;
        }

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($user, $sender, &$players) : void {
            if($onlinePlayer->getName() === $sender->getName() || $user->isInRtpData($onlinePlayer->getName()))
                return;

            $players[] = $onlinePlayer->getName();
        });

        if(empty($players)){
            $sender->sendMessage(MessageUtil::format("Na serwerze nie ma innych graczy!"));
            return;
        }

        $randomPlayer = $sender->getServer()->getPlayerExact($players[(mt_rand(0, (count($players) - 1)))]);

        $user->addRtp($randomPlayer->getName());

        $sender->teleport($randomPlayer->getPosition());
        $sender->sendMessage(MessageUtil::format("Przeteleportowano do gracza o nicku: §e" . $randomPlayer->getName()));
        AdminManager::sendMessage($sender, $sender->getName() . " zostal losowo przeteleportowany do " . $randomPlayer->getName());
    }
}