<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\Main;
use core\managers\nameTag\NameTagPlayerManager;
use core\utils\Settings;
use core\utils\SkinUtil;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;

class PlayerLoginListener implements Listener {

    /**
     * @param PlayerPreLoginEvent $e
     * @private LOWEST
     * @ignoreCancelled true
     */
    public function userOnLogin(PlayerPreLoginEvent $e) : void {
        $player = $e->getPlayerInfo();

        if(!Main::getInstance()->getUserManager()->getUser($player->getUsername())) {
            Main::getInstance()->getUserManager()->createUser($player);
        }

        if(($user = Main::getInstance()->getUserManager()->getUser($player->getUsername()))) {
            $user->connect();
            $user->getStatManager()->setStat(Settings::$STAT_LAST_JOIN_TIME, time());
        }
    }

    public function nameTagUpdate(PlayerLoginEvent $e) : void {
        $player = $e->getPlayer();

        if(!(NameTagPlayerManager::getNameTagData($player->getName()))) {
            NameTagPlayerManager::createNameTagData($player->getName());
        }
    }

    /**
     * @param PlayerLoginEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onLoginWings(PlayerLoginEvent $e) : void {

        $player = $e->getPlayer();
        $skin = $player->getSkin();

        $data = $skin->getSkinData();

        if(!$player->getName()) {
            $e->cancel();
            return;
        }

        $image = SkinUtil::skinDataToImage($data);

        if($image === null || imagesx($image) * imagesy($image) * 4 !== 16384) {
            Main::getInstance()->getSkinManager()->setPlayerDefaultSkin($player->getName());
            return;
        }

        Main::getInstance()->getSkinManager()->setPlayerSkinImage($player->getName(), $image);
    }

    public function permissionOnLogin(PlayerLoginEvent $e) : void {
        $player = $e->getPlayer();
        $groupManager = Main::getInstance()->getPlayerGroupManager();

        if(!$groupManager->isRegistered($player->getName())) {
            $groupManager->registerPlayer($player->getName());
        }

        $groupManager->getPlayer($player->getName())->joinPlayer();
    }
}