<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\Main;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use pocketmine\event\player\PlayerDeathEvent;

class DeathListener extends BaseListener{

    /**
     * @param PlayerDeathEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function deathMessage(PlayerDeathEvent $e) : void{
        $e->setDeathMessage("");
    }

    /**
     * @param PlayerDeathEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onDeathSetXp(PlayerDeathEvent $e) : void {
        $e->getPlayer()->setCurrentTotalXp(0);
    }

    /**
     * @param PlayerDeathEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onDeathKeepInventory(PlayerDeathEvent $e) : void{
        if(UserManager::getUser($e->getPlayer()->getName())->hasSkill(7) && !isset(Main::$antylogout[$e->getPlayer()->getName()]) && $e->getPlayer()->getLevel()->getName() !== ConfigUtil::PVP_WORLD)
            $e->setKeepInventory(true);
    }
}