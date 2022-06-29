<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\Main;
use core\manager\managers\SettingsManager;
use core\manager\managers\StatsManager;
use core\permission\managers\NameTagManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;

class LevelChangeEvent extends BaseListener{
    public function updateNameTagOnChangeLevel(EntityLevelChangeEvent $e) {
        $entity = $e->getEntity();

        if($entity instanceof Player)
            NameTagManager::updateNameTag($entity);
    }

    /**
     * @param EntityLevelChangeEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onLevelChangeInSpawn(EntityLevelChangeEvent $e) : void{

        $entity = $e->getEntity();

        if(!$entity instanceof Player)
            return;

        if(!$entity->isSurvival())
            return;

        if($e->getTarget()->getName() === ConfigUtil::DEFAULT_WORLD)
            $entity->setGamemode(2);
        else
            $entity->setGamemode(2);
    }

    /**
     * @param EntityLevelChangeEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     * @o
     */
    public function coordinatesOnLevelChange(EntityLevelChangeEvent $e) : void{
        $entity = $e->getEntity();

        if(!$entity instanceof Player)
            return;

        if(UserManager::getUser($entity->getName()) === null)
            return;

        $cords = UserManager::getUser($entity->getName())->isSettingEnabled(SettingsManager::COORDINATES);

        if($cords) {
            $pk = new GameRulesChangedPacket();
            $pk->gameRules = ["showcoordinates" => [1, true]];
        } else {
            $pk = new GameRulesChangedPacket();
            $pk->gameRules = ["showcoordinates" => [1, false]];
        }

        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($pk, $entity) : void{
            if($entity !== null)
                $entity->dataPacket($pk);
        }), 20);
    }

    public function levelChangeWhitelist(EntityLevelChangeEvent $e) : void{
        $entity = $e->getEntity();

        if(!$entity instanceof Player)
            return;

        if($e->getOrigin() === ConfigUtil::LOBBY_WORLD || $e->getTarget() !== ConfigUtil::LOBBY_WORLD){
            foreach($this->getServer()->getOnlinePlayers() as $serverPlayer) {
                if($entity === $serverPlayer || $serverPlayer->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
                    continue;

                $entity->showPlayer($serverPlayer);
                $serverPlayer->showPlayer($entity);
            }
        }
    }

    public function resetKillStreak(EntityLevelChangeEvent $e) : void {
        $entity = $e->getEntity();

        if(!$entity instanceof Player)
            return;

        $user = UserManager::getUser($entity->getName());

        if(!$user)
            return;

        if($e->getOrigin()->getName() === ConfigUtil::PVP_WORLD)
            $user->setStat(StatsManager::KILL_STREAK, 0);
    }
}