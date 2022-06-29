<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\manager\managers\terrain\TerrainManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use pocketmine\event\player\PlayerExhaustEvent;

class ExhaustListener extends BaseListener{
    public function FixFeed(PlayerExhaustEvent $e) : void {
        if($user = UserManager::getUser($e->getPlayer()->getName())) {
            if($user->hasSkill(8))
                $e->setAmount(($e->getAmount() / 2));
        }
    }

    public function lobbyExhaustBlock(PlayerExhaustEvent $e) {
        if($e->getPlayer()->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
            $e->setCancelled(true);
    }

    /**
     * @param PlayerExhaustEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function protectFood(PlayerExhaustEvent $e) : void {
        $player = $e->getPlayer();

        $terrain = TerrainManager::getPriorityTerrain($player->asPosition());

        if($player->isOp())
            return;

        if($terrain !== null){
            if(!$terrain->isSettingEnabled("lose_food"))
                $e->setCancelled(true);
        }
    }
}