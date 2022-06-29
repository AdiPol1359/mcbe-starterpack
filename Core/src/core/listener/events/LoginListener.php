<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\user\UserManager;
use pocketmine\event\player\PlayerLoginEvent;

class LoginListener extends BaseListener{

    /**
     * @param PlayerLoginEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function LoginCheckXUID(PlayerLoginEvent $e) : void{

        $player = $e->getPlayer();

        if(!UserManager::getUser($player->getName())){
            UserManager::createUser($player);
            return;
        }

        if(UserManager::getUser($player->getName())->getXUID() !== $player->getXuid()) {
            $e->setCancelled(true);
            $player->close("", "§l§4XUID ERROR§r§7!");
        }
    }
}