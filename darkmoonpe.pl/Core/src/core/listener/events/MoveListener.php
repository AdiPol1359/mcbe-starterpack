<?php

namespace core\listener\events;

use core\caveblock\CaveManager;
use core\listener\BaseListener;
use core\Main;
use core\manager\managers\particle\ParticleManager;
use core\manager\managers\SettingsManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use pocketmine\event\player\PlayerMoveEvent;

class MoveListener extends BaseListener{

    public function onMoveAtTheTopOfLevel(PlayerMoveEvent $e) {

        if($e->getFrom()->floor()->equals($e->getTo()->floor()))
            return;

        $player = $e->getPlayer();
        $y = $player->getY();

        if(!CaveManager::isInCave($player))
            return;

        if($y >= 99)
            $e->setCancelled(true);
    }

    public function CaveBorder(PlayerMoveEvent $e) {

        if($e->getFrom()->floor()->equals($e->getTo()->floor()))
            return;

        if(!CaveManager::isInCave($e->getPlayer()))
            return;

        $player = $e->getPlayer();
        $x = $player->getFloorX();
        $z = $player->getFloorZ();

        $border = ConfigUtil::CAVE_BORDER;

        if(abs($x) >= ($border - 10)) {
            $distance = 10 - (abs($x) - ($border - 10));
            $distance < 0 ? $distance = 0 : null;
            $player->sendTip("§7BORDER: §l§8".substr_replace("||||||||||", "§9".str_repeat("|", $distance)."§8", 0, $distance));
        }

        if(abs($z) >= ($border - 10)) {
            $distance = 10 - (abs($z) - ($border - 10));
            $distance < 0 ? $distance = 0 : null;
            $player->sendTip("§7BORDER: §l§8".substr_replace("||||||||||", "§9".str_repeat("|", $distance)."§8", 0, $distance));
        }

        if($x >= $border || $x <= -$border || $z >= $border || $z <= -$border)
            $e->setCancelled(true);

        if(abs($x) >= ($border + 10) || abs($z) >= ($border + 10))
            $player->kill();
    }

    public function lobbyMoveBlock(PlayerMoveEvent $e) {
        if($e->getPlayer()->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
            $e->setCancelled(true);
    }

    /**
     * @param PlayerMoveEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onMoveInSpawn(PlayerMoveEvent $e) : void{
        if($e->getFrom()->floor()->equals($e->getTo()->floor()))
            return;

        if(!$e->getPlayer()->isSurvival())
            return;

        if($e->getPlayer()->getLevel()->getName() === ConfigUtil::DEFAULT_WORLD)
            $e->getPlayer()->setGamemode(2);
        else
            $e->getPlayer()->setGamemode(0);
    }

    /**
     * @param PlayerMoveEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function autoSprint(PlayerMoveEvent $e) : void{

        if($e->getFrom()->equals($e->getTo()))
            return;

        if($e->getPlayer()->getFood() <= 6)
            return;

        if(UserManager::getUser($e->getPlayer()->getName())->isSettingEnabled(SettingsManager::AUTO_SPRINT))
            $e->getPlayer()->setSprinting(true);
    }

    /**
     * @param PlayerMoveEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function particleSpawn(PlayerMoveEvent $e) : void {

        if($e->getFrom()->round(1)->equals($e->getTo()->round(1)))
            return;

        $player = $e->getPlayer();

        foreach(ParticleManager::getParticles() as $particle) {

            if(!$particle->onMove() || !$particle->hasPlayer($player->getName()))
                continue;

            if($player->getLevel()->getName() === ConfigUtil::PVP_WORLD || $player->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
                continue;

            $particle->onSpawn($player);
        }
    }

    public function onMoveTeleport(PlayerMoveEvent $e) : void {

        if($e->getFrom()->floor()->equals($e->getTo()->floor()))
            return;

        $player = $e->getPlayer();

        if(isset(Main::$teleportPlayers[$player->getName()]))
            Main::$teleportPlayers[$player->getName()]->getTask()->stop();
    }
}