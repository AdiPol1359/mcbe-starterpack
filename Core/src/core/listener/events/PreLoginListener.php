<?php

namespace core\listener\events;

use core\caveblock\CaveManager;
use core\listener\BaseListener;
use pocketmine\event\player\PlayerPreLoginEvent;

class PreLoginListener extends BaseListener {

    /**
     * @param PlayerPreLoginEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function loadCaves(PlayerPreLoginEvent $e) : void {
        $player = $e->getPlayer();

        if(CaveManager::hasCave($player))
            CaveManager::loadCaves($player);
    }
}