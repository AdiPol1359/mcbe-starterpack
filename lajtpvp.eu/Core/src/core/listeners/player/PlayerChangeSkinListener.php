<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChangeSkinEvent;

class PlayerChangeSkinListener implements Listener{

    public function checkSkin(PlayerChangeSkinEvent $e) : void{
        $player = $e->getPlayer();

        if(($wings = Main::getInstance()->getWingsManager()->getPlayerWings($player->getName())) !== null) {
            Main::getInstance()->getWingsManager()->setWings($player, $wings);
        }

        Main::getInstance()->getSkinManager()->setPlayerSkin($player, $player->getSkin());
    }
}