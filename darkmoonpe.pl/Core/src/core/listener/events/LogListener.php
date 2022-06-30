<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\manager\managers\LogManager;
use core\util\utils\ConfigUtil;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;

class LogListener extends BaseListener{
    public function JoinLog(PlayerJoinEvent $e) : void{
        if($e->getPlayer()->hasPermission(ConfigUtil::PERMISSION_TAG."administrator"))
            LogManager::sendLog($e->getPlayer(), "Joined", LogManager::ADMIN_LOG);
    }

    public function QuitLog(PlayerQuitEvent $e) : void{
        if($e->getPlayer()->hasPermission(ConfigUtil::PERMISSION_TAG."administrator"))
            LogManager::sendLog($e->getPlayer(), "Quit", LogManager::ADMIN_LOG);
    }

    public function DropLog(PlayerDropItemEvent $e) : void{
        if($e->getPlayer()->hasPermission(ConfigUtil::PERMISSION_TAG."administrator"))
            LogManager::sendLog($e->getPlayer(), "Drop: ".$e->getItem()->getId().":".$e->getItem()->getDamage().":".$e->getItem()->getCount(), LogManager::ADMIN_LOG);
    }

    public function CommandLog(PlayerCommandPreprocessEvent $e) : void{
        if($e->getMessage()[0] != '/')
            return;

        if($e->getPlayer()->hasPermission(ConfigUtil::PERMISSION_TAG."administrator"))
            LogManager::sendLog($e->getPlayer(), "Command: ".$e->getMessage(), LogManager::ADMIN_LOG);
    }

    public function KillLog(EntityDamageEvent $e) : void{
        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        $entity = $e->getEntity();
        $damager = $e->getDamager();

        if(!$damager instanceof Player || !$entity instanceof Player)
            return;

        if($damager->hasPermission(ConfigUtil::PERMISSION_TAG."administrator"))
            LogManager::sendLog($damager, "Kill: ".$entity->getName(), LogManager::ADMIN_LOG);
    }
}