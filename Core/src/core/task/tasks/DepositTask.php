<?php

namespace core\task\tasks;

use core\manager\managers\StatsManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class DepositTask extends Task {

    public function onRun(int $currentTick) {

        foreach(Server::getInstance()->getOnlinePlayers() as $player) {

            if($player->getLevelNonNull()->getName() !== ConfigUtil::PVP_WORLD)
                continue;

            $user = UserManager::getUser($player->getName());

            $koxy = 0;
            $refy = 0;
            $perly = 0;

            foreach($player->getInventory()->getContents() as $item) {
                if($item->getId() == 466)
                    $koxy += $item->getCount();

                if($item->getId() == 322)
                    $refy += $item->getCount();

                if($item->getId() == 368)
                    $perly += $item->getCount();
            }

            if($koxy > ConfigUtil::LIMIT_KOXY) {
                $rKoxy = $koxy - ConfigUtil::LIMIT_KOXY;

                $player->getInventory()->removeItem(Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, $rKoxy));

                $user->addToStat(StatsManager::KOXY, $rKoxy);

                $player->sendMessage(MessageUtil::format("Twoj nadmiar koxow zostal przeniesiony do schowka!"));
            }

            if($refy > ConfigUtil::LIMIT_REFY) {
                $rRefy = $refy - ConfigUtil::LIMIT_REFY;

                $player->getInventory()->removeItem(Item::get(Item::GOLDEN_APPLE, 0, $rRefy));

                $user->addToStat(StatsManager::REFY, $rRefy);

                $player->sendMessage(MessageUtil::format("Twoj nadmiar refow zostal przeniesiony do schowka!"));
            }

            if($perly > ConfigUtil::LIMIT_PERLY) {
                $rPerly = $perly - ConfigUtil::LIMIT_PERLY;

                $player->getInventory()->removeItem(Item::get(Item::ENDER_PEARL, 0, $rPerly));

                $user->addToStat(StatsManager::PERLY, $rPerly);

                $player->sendMessage(MessageUtil::format("Twoj nadmiar perel zostal przeniesiony do schowka!"));
            }
        }
    }
}