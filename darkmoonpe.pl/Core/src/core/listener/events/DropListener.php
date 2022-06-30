<?php

namespace core\listener\events;

use core\caveblock\CaveManager;
use core\listener\BaseListener;
use core\manager\managers\BanManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\event\player\PlayerDropItemEvent;

class DropListener extends BaseListener{

    public function isBannedOnDropItem(PlayerDropItemEvent $e) : void {
        $player = $e->getPlayer();
        if(BanManager::isBanned($player->getName()) || $player->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
            $e->setCancelled(true);
    }

    public function onDropItemEvent(PlayerDropItemEvent $e) {

        $player = $e->getPlayer();

        if($player->isOp() || $player->hasPermission(ConfigUtil::PERMISSION_TAG."admin.cave"))
            return;

        if(!CaveManager::isInCave($player))
            return;

        $cave = CaveManager::getCave($player);

        if($cave->isOwner($player->getName()))
            return;

        if(!$cave){
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz nie masz uprawnien"));
            return;
        }

        if(!$cave->isMember($player->getName())){
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz nie jest to twoja jaskinia"));
            return;
        }

        if(!$cave->getPlayerSetting($player->getName(), "d_item")) {

            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz nie masz uprawnien"));
            return;
        }
    }
}