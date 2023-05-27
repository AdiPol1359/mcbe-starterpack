<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\Main;
use core\utils\Settings;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\player\Player;

class PlayerExhaustListener implements Listener {

    /**
     * @param PlayerExhaustEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function protectFood(PlayerExhaustEvent $e) : void {
        $player = $e->getPlayer();

        if(!$player instanceof Player)
            return;

        $terrain = Main::getInstance()->getTerrainManager()->getPriorityTerrain($player->getPosition());

        if($player->getServer()->isOp($player->getName()))
            return;

        if($terrain !== null){
            if(!$terrain->isSettingEnabled(Settings::$TERRAIN_LOSE_FOOD))
                $e->cancel();
        }
    }

    public function losingFood(PlayerExhaustEvent $e) : void{
        if($e->getCause() == PlayerExhaustEvent::CAUSE_SPRINT_JUMPING || $e->getCause() == PlayerExhaustEvent::CAUSE_SPRINTING) {
            $e->setAmount(($e->getAmount() / 4));
        }
    }
}