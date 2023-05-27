<?php

declare(strict_types=1);

namespace core\listeners\entity;

use core\Main;
use core\utils\SoundUtil;
use core\managers\TeleportManager;
use core\managers\war\War;
use core\permissions\managers\FormatManager;
use core\tasks\sync\TeleportTask;
use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use core\utils\Settings;
use core\utils\TimeUtil;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\world\Position;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class EntityDamageListener implements Listener {

    /**
     * @param EntityDamageEvent $e
     * @priority LOWEST
     * @ignoreCancelled true
     */
    public function projectDamage(EntityDamageEvent $e) : void {

        $entity = $e->getEntity();
        $entityPosition = $entity->getPosition()->round();

        $entityTerrain = Main::getInstance()->getTerrainManager()->getPriorityTerrain(Position::fromObject($entityPosition, $entity->getWorld()));

        if($e instanceof EntityDamageByEntityEvent) {

            $damager = $e->getDamager();

            if(!$damager)
                return;

            $damagerPosition = null;

            if($damager instanceof Player)
                $damagerPosition = $damager->getPosition()->round();
            else {
                if($damager->getOwningEntity())
                    $damagerPosition = $damager->getOwningEntity()->getPosition()->round();
                else
                    return;
            }

            $damagerTerrain = Main::getInstance()->getTerrainManager()->getPriorityTerrain(Position::fromObject($damagerPosition, $entity->getWorld()));

            if($entityTerrain !== null) {
                if(!$entityTerrain->isSettingEnabled(Settings::$TERRAIN_FIGHTING))
                    $e->cancel();
            }

            if($damagerTerrain !== null) {
                if(!$damagerTerrain->isSettingEnabled(Settings::$TERRAIN_FIGHTING))
                    $e->cancel();
            }

        } else {
            if($entityTerrain !== null) {
                if(!$entityTerrain->isSettingEnabled(Settings::$TERRAIN_DAMAGE))
                    $e->cancel();
            }
        }
    }

    /**
     * @param EntityDamageEvent $e
     * @priority LOW
     * @ignoreCancelled true
     */
    public function guildFriendlyFire(EntityDamageEvent $e) : void {

        if(!$e instanceof EntityDamageByEntityEvent || $e->isCancelled())
            return;

        $damager = $e->getDamager();
        $entity = $e->getEntity();

        if(!$entity instanceof Player)
            return;

        if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($entity->getName())) !== null) {

            $mainDamager = null;

            if($damager instanceof Player)
                $mainDamager = $damager;
            else if($damager->getOwningEntity() instanceof Player)
                $mainDamager = $damager->getOwningEntity();

            if(!$mainDamager)
                return;

            if($mainDamager->getName() === $entity->getName())
                return;

            if($guild->existsPlayer($mainDamager->getName())) {
                if(!$guild->isFriendlyFireEnabled())
                    $e->cancel();
                else
                    $e->setModifier(Settings::$INT32_MIN, EntityDamageEvent::MODIFIER_ARMOR);
            }

            if($guild->isAlliancePlayer($mainDamager->getName())) {
                if(!$guild->isAlliancePvpEnabled())
                    $e->cancel();
                else
                    $e->setModifier(Settings::$INT32_MIN, EntityDamageEvent::MODIFIER_ARMOR);
            }
        }
    }

    /**
     * @param EntityDamageEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function safeDamage(EntityDamageEvent $e) : void {

        if($e->isCancelled())
            return;

        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        $entity = $e->getEntity();
        $damager = $e->getDamager();

        if(!$entity instanceof Player)
            return;

        $entityUser = Main::getInstance()->getUserManager()->getUser($entity->getName());
        $damagerUser = null;

        if($damager instanceof Player)
            $damagerUser = Main::getInstance()->getUserManager()->getUser($damager->getName());
        else {
            $owningEntity = $damager->getOwningEntity();

            if($owningEntity instanceof Player)
                $damagerUser = Main::getInstance()->getUserManager()->getUser($owningEntity->getName());
        }

        if(!$entityUser)
            return;

        if($entityUser->isSafe()) {

            if($damager instanceof Player)
                $damager->sendMessage(MessageUtil::format("Ten gracz ma jeszcze ochrone przez " . TimeUtil::convertIntToStringTime(($entityUser->getSafeTime() - time()), "§e", "§7", true, false)));

            $e->cancel();
        }

        if($damagerUser) {
            if($damagerUser->isSafe()) {
                $damager->sendMessage(MessageUtil::format("Nie mozesz atakowac poniewaz masz ochrone!"));
                $e->cancel();
            }
        }
    }

//    public function lobbyDamageBlock(EntityDamageEvent $e) {
//        if($e->getEntity()->getWorld()->getDisplayName() === Settings::$LOBBY_WORLD)
//            $e->cancel();
//    }

    public function whoCheck(EntityDamageEvent $e) : void {

        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        $damager = $e->getDamager();
        $entity = $e->getEntity();

        if(!$damager instanceof Player || !$entity instanceof Player)
            return;

        $user = Main::getInstance()->getUserManager()->getUser($damager->getName());

        if(!$user)
            return;

        if($user->hasLastData(Settings::$WHO)) {
            if(($data = $user->getLastData(Settings::$WHO)["value"]) >= time()) {
                $entity->setSneaking(false);
                $damager->sendMessage(MessageUtil::format("Nick gracza ktorego uderzyles to §e" . $entity->getName()));
            }

            $user->removeLastData(Settings::$WHO);
        }
    }

    public function blockHit(EntityDamageEvent $e) : void {
        if($e instanceof EntityDamageByEntityEvent) {
            $damager = $e->getDamager();
            if(!$damager instanceof Player)
                return;

            if(isset(Main::getInstance()->getCpsManager()->blockAttack[$damager->getName()]))
                $e->cancel();
        }
    }

    /**
     * @param EntityDamageEvent $e
     * @ignoreCancelled true
     */
    public function knockBack(EntityDamageEvent $e) : void {
        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        $e->setKnockBack(0.356);
        $e->setAttackCooldown(9);
    }

    public function interruptTeleport(EntityDamageEvent $e) : void {
        if($e->isCancelled())
            return;

        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        $entity = $e->getEntity();

        if(!$entity instanceof Player)
            return;

        if(!TeleportManager::isTeleporting($entity->getName()))
            return;

        $task = TeleportManager::getTeleport($entity->getName())->getTask();

        if($task instanceof TeleportTask)
            $task->stop();
    }

    public function teleportSafe(EntityDamageEvent $e) : void {
        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        $entity = $e->getEntity();

        if(!$entity instanceof Player)
            return;

        if(!($user = Main::getInstance()->getUserManager()->getUser($entity->getName())))
            return;

        if($user->hasLastData(Settings::$SAFE_TELEPORT))
            $e->cancel();
    }

    public function tntDamage(EntityDamageEvent $e) : void {
        if($e->getCause() !== $e::CAUSE_ENTITY_EXPLOSION)
            return;

        $e->setModifier(Settings::$INT32_MIN, EntityDamageEvent::MODIFIER_ARMOR);
    }

    /**
     * @param EntityDamageEvent $e
     * @priority HIGHEST
     */
    public function setAntyLogout(EntityDamageEvent $e) : void {

        if($e->isCancelled() || !$e instanceof EntityDamageByEntityEvent)
            return;

        $entity = $e->getEntity();
        $damager = $e->getDamager();

        if(!$entity instanceof Player || !$damager instanceof Player || $entity->getHealth() <= $e->getFinalDamage())
            return;

        if($damager->isCreative())
            return;

        if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($entity->getName())) !== null) {
            if($guild->existsPlayer($damager->getName()) || $guild->isAlliancePlayer($damager->getName()))
                return;
        }

        if($damager->isCreative()) {
            if(($terrain = Main::getInstance()->getTerrainManager()->getPriorityTerrain($damager->getPosition())) !== null) {
                if(!$terrain->isSettingEnabled(Settings::$TERRAIN_FIGHTING))
                    return;
            }
        }

        $entityUser = Main::getInstance()->getUserManager()->getUser($entity->getName());
        $damagerUser = Main::getInstance()->getUserManager()->getUser($damager->getName());
        $finalDamage = $e->getFinalDamage();

        if(!$entityUser || !$damagerUser)
            return;

        if($damager->getName() === $entity->getName())
            return;

        if(!$e->isCancelled()) {
            if($e->getFinalDamage() < $entity->getHealth())
                $entityUser->setAntyLogout($damager->getName(), $finalDamage);

            $damagerUser->setAntyLogout($entity->getName());
        }
    }

    /**
     * @param EntityDamageEvent $e
     * @priority HIGHEST
     */
    public function deathAntyLogout(EntityDamageEvent $e) : void {
        if($e->isCancelled())
            return;

        $entity = $e->getEntity();
        $damager = null;
        $finalDamage = $e->getFinalDamage();

        if(!$entity instanceof Player || $finalDamage < $entity->getHealth())
            return;

        $user = Main::getInstance()->getUserManager()->getUser($entity->getName());
        $userStat = $user->getStatManager();

        if($e instanceof EntityDamageByEntityEvent)
            $damager = $e->getDamager();

        if(!$user->hasAntyLogout()) {
            if($e instanceof EntityDamageByEntityEvent) {
                if(!$damager instanceof Player)
                    return;

                $lastAttackerUser = Main::getInstance()->getUserManager()->getUser($damager->getName());
                if(!$lastAttackerUser)
                    return;

                $user->setAntyLogout($damager->getName(), $finalDamage);
                $lastAttackerUser->setAntyLogout($entity->getName());
            }

            $userStat->reduceStat(Settings::$STAT_POINTS, 20);
            $entity->sendTitle("§l§cSAMOBOJSTWO", "§8(§c-20§8)", 20, 40, 20);

            foreach($entity->getInventory()->getContents(false) as $item)
                $entity->getWorld()->dropItem($entity->getPosition(), $item);

            foreach($entity->getArmorInventory()->getContents(false) as $item)
                $entity->getWorld()->dropItem($entity->getPosition(), $item);

            $entity->getWorld()->dropExperience($entity->getPosition(), $entity->getXpDropAmount());
        } else {
            $damagerName = $user->getLastAttacker();

            if($e instanceof EntityDamageByEntityEvent) {
                if($damager instanceof Player)
                    $damagerName = $damager->getName();
            }

            $damagerUser = Main::getInstance()->getUserManager()->getUser($damagerName);
            $damagerStat = $damagerUser->getStatManager();
            $damagerPlayer = Server::getInstance()->getPlayerExact($damagerName);

            $assistNick = null;
            $assistDamage = null;

            foreach($user->getAssists() as $assist => $damage) {
                if($assist === $damagerName)
                    continue;

                if($assistNick === null || $assistDamage < $damage) {
                    $assistNick = $assist;
                    $assistDamage = $damage;
                }
            }

            $assistPlayer = ($assistNick ? Server::getInstance()->getPlayerExact($assistNick) : null);
            $assistNick !== null ? $assistUser = Main::getInstance()->getUserManager()->getUser($assistNick) : $assistUser = null;

            $assistFormat = "";

            $entityPoints = $userStat->getStat(Settings::$STAT_POINTS) <= 0 ? 1 : $userStat->getStat(Settings::$STAT_POINTS);
            $damagerPoints = $damagerStat->getStat(Settings::$STAT_POINTS) <= 0 ? 1 : $damagerStat->getStat(Settings::$STAT_POINTS);

            $percentage = $entityPoints * 0.03;
            if($damagerPoints <= $entityPoints) {
                $value = ($entityPoints - $damagerPoints) / $damagerPoints + 1;
                $addPoints = round($percentage * $value);
                $removePoints = round($percentage);
            } else {
                $value = ($damagerPoints - $entityPoints) / $entityPoints + 1;
                $addPoints = round($percentage / $value);
                $removePoints = round($percentage / ($value * $value));
            }

            $assistPoints = round($addPoints / 2);

            $removePoints < 10 ? $removePoints = 10 : ($removePoints > 100 ? $removePoints = 100 : null);
            $addPoints < 10 ? $addPoints = 10 : ($addPoints > 100 ? $addPoints = 100 : null);
            $assistPoints < 5 ? $assistPoints = 5 : ($assistPoints > 100 ? $assistPoints = 100 : null);

            if($damagerUser->hasKilled($entity->getName(), $entity->getNetworkSession()->getIp())) {
                $removePoints = 0;
                $addPoints = 1;
            }

            if($assistUser) {
                if($assistUser->hasKilled($entity->getName(), $entity->getNetworkSession()->getIp()))
                    $assistPoints = 1;
            }

            if($userStat->getStat(Settings::$STAT_POINTS) <= 0)
                $addPoints = 0;

            if(($damagerGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($damagerName)))
                $damagerGuild->addPoints((int)round((int)$addPoints / 10));

            if(($entityGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($entity->getName())))
                $entityGuild->reducePoints((int)round((int)$removePoints / 10));

            if($damagerGuild !== null && $entityGuild !== null) {
                if(($war = Main::getInstance()->getWarManager()->getWar($damagerGuild->getTag())) !== null) {
                    if($war->getStartTime() <= time()) {
                        if($war->getAttacker() === $entityGuild->getTag()) {
                            $war->addStatAttacker(War::STAT_DEATHS, 1);
                            $war->addStatAttacked(War::STAT_KILLS, 1);
                        } else if($war->getAttacked() === $entityGuild->getTag()) {
                            $war->addStatAttacked(War::STAT_DEATHS, 1);
                            $war->addStatAttacker(War::STAT_KILLS, 1);
                        }
                    }
                }
            }

            $assistGuild = ($assistNick ? Main::getInstance()->getGuildManager()->getPlayerGuild($assistNick) : null);

            if($assistNick !== null)
                $assistFormat = "§7 z pomoca " . ($assistGuild !== null ? "{TAG}" . " " : "") . "§e" . $assistNick . " §8(§a+" . $assistPoints . "§8)";

            $entity->sendTitle("§l§cSMIERC", "§r§7" . $damagerName . " §8(§c-" . $removePoints . "§8)", 20, 40, 20);

            if($damagerPlayer) {
                $damagerPlayer->sendTitle("§l§aZABOJSTWO", "§r§7" . $entity->getName() . " §8(§a+" . $addPoints . "§7§8)", 20, 40, 20);

                foreach($entity->getInventory()->getContents(false) as $item) {
                    if($damagerPlayer->getInventory()->canAddItem($item))
                        $damagerPlayer->getInventory()->addItem($item); else
                        $damagerPlayer->getWorld()->dropItem($damagerPlayer->getPosition(), $item);
                }

                foreach($entity->getArmorInventory()->getContents(false) as $item) {
                    if($damagerPlayer->getInventory()->canAddItem($item))
                        $damagerPlayer->getInventory()->addItem($item); else
                        $damagerPlayer->getWorld()->dropItem($damagerPlayer->getPosition(), $item);
                }

                $damagerPlayer->getXpManager()->addXp($entity->getXpDropAmount(), false);
                SoundUtil::addSound([$damagerPlayer], $damagerPlayer->getPosition(), "random.levelup");
            } else {
                foreach($entity->getInventory()->getContents(false) as $item)
                    $entity->getWorld()->dropItem($entity->getPosition(), $item);
                foreach($entity->getArmorInventory()->getContents(false) as $item)
                    $entity->getWorld()->dropItem($entity->getPosition(), $item);

                $entity->getWorld()->dropExperience($entity->getPosition(), $entity->getXpDropAmount());
            }

            $damagerStat->addStat(Settings::$STAT_KILLS);
            $damagerStat->addStat(Settings::$STAT_POINTS, $addPoints);
            $damagerUser->addKilledPlayer($entity->getName(), $entity->getNetworkSession()->getIp());

            $userStat->reduceStat(Settings::$STAT_POINTS, $removePoints);
            $userStat->addStat(Settings::$STAT_DEATHS);

            if($assistUser) {
                $assistStat = $assistUser->getStatManager();

                $damagerUser->addKilledPlayer($entity->getName(), $entity->getNetworkSession()->getIp());

                $assistStat->addStat(Settings::$STAT_ASSISTS);
                $assistStat->addStat(Settings::$STAT_POINTS, $assistPoints);

                $assistGuild?->addPoints((int)round((int)$assistPoints / 10));
            }

            $assistPlayer?->sendTitle("§l§eASYSTA", "§r§7" . $entity->getName() . " §8(§a+" . $assistPoints . "§7§8)", 20, 40, 20);

            $messages = FormatManager::guildFormatMessage(MessageUtil::format(($entityGuild !== null ? "{TAG}" . " " : "") . "§e" . $entity->getName() . " §8(§c-" . $removePoints . "§8) §7zostal zabity przez " . ($damagerGuild !== null ? "{TAG}" . " " : "") . "§e" . $damagerName . " §8(§a+" . $addPoints . "§8)" . $assistFormat), [($entityGuild === null ? "" : $entityGuild->getTag()), ($damagerGuild === null ? "" : $damagerGuild->getTag()), ($assistGuild === null ? "" : $assistGuild->getTag())], Server::getInstance()->getOnlinePlayers());

            BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($messages) : void {
                if(isset($messages[$onlinePlayer->getName()]))
                    $onlinePlayer->sendMessage($messages[$onlinePlayer->getName()]);
            });
        }

        $entity->extinguish();
        $viewers = $entity->getViewers();
        $position = $entity->getPosition();

        $pk = new AddActorPacket();
        $pk->type = "minecraft:lightning_bolt";
        $pk->actorRuntimeId = Entity::nextRuntimeId();
        $pk->position = $position;

        $entity->getServer()->broadcastPackets($viewers, [$pk]);

        SoundUtil::addSound($entity->getViewers(), $position, "ambient.weather.lightning.impact");

        $entity->setHealth($entity->getMaxHealth());
        $entity->getHungerManager()->setFood(20);
        $entity->getHungerManager()->setSaturation(20);
        $entity->getEffects()->clear();

        $entity->getInventory()->clearAll();
        $entity->getArmorInventory()->clearAll();
        $e->cancel();

        $user->clearEnderPearls();

        $entity->teleport($entity->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
        $entity->setHealth($entity->getMaxHealth());
        $user->resetAntyLogout();

        $entity->getXpManager()->setCurrentTotalXp(0);

        SoundUtil::addSound([$entity], $position, "random.explode");

        $user->resetAntyLogout();

        if($damager instanceof Player)
            $damager->extinguish();
    }

    /**
     * @param EntityDamageEvent $e
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function godMode(EntityDamageEvent $e) : void {
        if($e->isCancelled())
            return;

        $player = $e->getEntity();

        if(!$player instanceof Player)
            return;

        if(($user = Main::getInstance()->getUserManager()->getUser($player->getName()))) {
            if($user->hasGod()) {
                $e->setModifier(Settings::$INT32_MIN, EntityDamageEvent::MODIFIER_ARMOR);
            }
        }
    }
}