<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\Main;
use core\managers\BorderPlayerManager;
use core\managers\bossbar\BossbarManager;
use core\managers\TeleportManager;
use core\tasks\sync\TeleportTask;
use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use core\utils\Settings;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\GameMode;

class PlayerQuitListener implements Listener {

    /**
     * @param PlayerQuitEvent $e
     * @priority LOWEST
     * @ignoreCancelled true
     */
    public function userProfile(PlayerQuitEvent $e) {
        $player = $e->getPlayer();

        if(!($user = Main::getInstance()->getUserManager()->getUser($player->getName()))) {
            return;
        }

        $user->disconnect();
    }

    public function permissionOnQuit(PlayerQuitEvent $e) : void {
        Main::getInstance()->getPlayerGroupManager()->getPlayer($e->getPlayer()->getName())->quitPlayer();
    }

    public function messageOnQuit(PlayerQuitEvent $e) : void {
        $e->setQuitMessage("");
    }

    public function onQuitTeleport(PlayerQuitEvent $e) : void {

        $player = $e->getPlayer();

        if(!TeleportManager::isTeleporting($player->getName()))
            return;

        $task = TeleportManager::getTeleport($player->getName())->getTask();

        if($task instanceof TeleportTask)
            $task->stop();
    }

    public function LogOutSpr(PlayerQuitEvent $e) : void {
        $player = $e->getPlayer();

        if(isset(Settings::$VERIFY[$player->getName()])) {
            unset(Settings::$VERIFY[$player->getName()]);

            if(Main::getInstance()->getBanManager()->isBanned($player->getName()))
                return;

            BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($player) : void {
                $onlinePlayer->sendMessage(MessageUtil::formatLines(["Gracz o nicku §e".$player->getName(), "§7Wylogowal sie podczas sprawdzania!"]));
            });
        }
    }

    public function antylogoutOnQuit(PlayerQuitEvent $e) : void {
        $player = $e->getPlayer();

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if(!$user)
            return;

        if($user->hasAntyLogout())
            $player->attack(new EntityDamageEvent($player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 999));
    }

    public function quitOnTerrain(PlayerQuitEvent $e) : void {
        $player = $e->getPlayer();
        $pos = $player->getPosition();

        if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($pos)) !== null) {
            if(!$guild->existsPlayer($player->getName())) {
                if($pos->distance($guild->getHeartSpawn()) <= 20)
                    $player->teleport($pos->withComponents($pos->x, ($pos->getWorld()->getHighestBlockAt($pos->x, $pos->z) + 1), $pos->z));
            }
        }
    }

    public function timePlayed(PlayerQuitEvent $e) : void {
        $player = $e->getPlayer();

        if(!($user = Main::getInstance()->getUserManager()->getUser($player->getName())))
            return;

        $user->getStatManager()->addStat(Settings::$STAT_SPEND_TIME, (time() - $user->getStatManager()->getStat(Settings::$STAT_LAST_JOIN_TIME)));
    }

    public function bossbarOnQuit(PlayerQuitEvent $e) : void {
        $player = $e->getPlayer();

        if(($bossbar = BossbarManager::getBossbar($player)) !== null) {
            BossbarManager::unsetBossbar($player);
            if($player->isAdventure() && !$player->isSpectator())
                $player->setGamemode(GameMode::SURVIVAL());
        }
    }

    public function onQuitBorder(PlayerQuitEvent $e) : void {
        BorderPlayerManager::removePlayer($e->getPlayer()->getName());
    }
}