<?php

namespace core\anticheat\module\modules\data;

use core\anticheat\AntiCheatManager;
use core\Main;
use pocketmine\entity\Effect;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class DistPlayerCalculator implements Listener {

    private static array $data = [];
    
    /**
     * @param PlayerMoveEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function onMoveSpeed(PlayerMoveEvent $e) : void {

        if(!AntiCheatManager::getAntiCheatByName("Speed")->isModuleEnabled() || !AntiCheatManager::getAntiCheatByName("TeleportHack")->isModuleEnabled())
            return;

        $from = $e->getFrom();
        $to = $e->getTo();

        $player = $e->getPlayer();

        if($player->getPing() >= 500)
            return;

        if($player->isFlying() || $player->isGliding() && !$e->getFrom()->floor()->equals($e->getTo()->floor()))
            self::$data["lastFlyTick"][$player->getName()] = Server::getInstance()->getTick();

        if($player->isFlying() || $player->isGliding() || $player->fallDistance >= 2)
            return;

        if(isset(self::$data["lastFlyTick"][$player->getName()]) && (self::$data["lastFlyTick"][$player->getName()] + 40) > Server::getInstance()->getTick())
            return;

        $dist = sqrt(round(pow($from->x - $to->x, 2) + pow($from->z - $to->z, 2), 3));

        if(isset(self::$data["dist"][$player->getName()])) {
            if((count(self::$data["dist"][$player->getName()])) >= 50)
                unset(self::$data["dist"][$player->getName()][array_key_first(self::$data["dist"][$player->getName()])]);
        }

        if(isset(self::$data["packetTime"][$player->getName()])) {
            if((count(self::$data["packetTime"][$player->getName()])) >= 50)
                unset(self::$data["packetTime"][$player->getName()][array_key_first(self::$data["packetTime"][$player->getName()])]);
        }

        if(!$e->getFrom()->equals($e->getTo())) {

            $remove = 0;

            foreach($player->getEffects() as $effectInstance) {
                if($effectInstance->getId() === Effect::SPEED)
                    $remove += ($effectInstance->getAmplifier() / 10);
            }

            if($player->fallDistance >= 5)
                $remove += ($player->fallDistance / 4);

            if($player->getPing() >= 80)
                $remove += ($player->getPing() / 2500);

            $b = array_filter(self::$data["packetTime"][$player->getName()]);

            if(!empty($b)) {
                $averagePacket = (microtime(true) - (array_sum($b) / count($b)));

                if($averagePacket <= 5) {
                    if($averagePacket >= 2)
                        $remove += ($averagePacket / 6);
                }
            }

            self::$data["packetTime"][$player->getName()][] = (microtime(true) - (self::$data["lastPacket"][$player->getName()] >= 10 ? 0 : self::$data["lastPacket"][$player->getName()]));
            self::$data["lastPacket"][$player->getName()] = 0;

            if(($dist - $remove) < 0)
                return;

            self::$data["dist"][$player->getName()][] = ($dist - $remove);
        }

        self::$data["lastPacket"][$player->getName()] = microtime(true);
    }

    public static function onTeleport(EntityTeleportEvent $e) : void {
        $entity = $e->getEntity();

        if(!$entity instanceof Player)
            return;

        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($entity) : void {
            if(!$entity)
                return;

            self::$data["dist"][$entity->getName()] = [];
            self::$data["packetTime"][$entity->getName()] = [];
            self::$data["lastPacket"][$entity->getName()] = 0;
        }), 10);
    }
    
    public static function getDist() : array {
        return self::$data["dist"];
    }

    public static function getPacketTime() : array {
        return self::$data["packetTime"];
    }

    public static function removeHighestDist(string $nick) : void {
        if(self::$data["dist"][$nick]) {

            foreach(array_keys(self::$data["dist"][$nick], max(self::$data["dist"][$nick])) as $key)
                unset(self::$data["dist"][$nick][$key]);
        }
    }

    public function onJoinDist(PlayerJoinEvent $e) : void {
        self::$data["dist"][$e->getPlayer()->getName()] = [];
        self::$data["packetTime"][$e->getPlayer()->getName()] = [];
        self::$data["lastPacket"][$e->getPlayer()->getName()] = 0;
    }

    public function onQuitDist(PlayerQuitEvent $e) : void {
        if(isset(self::$data["dist"][$e->getPlayer()->getName()]))
            unset(self::$data["dist"][$e->getPlayer()->getName()]);

        if(isset(self::$data["packetTime"][$e->getPlayer()->getName()]))
            unset(self::$data["packetTime"][$e->getPlayer()->getName()]);

        if(isset(self::$data["lastPacket"][$e->getPlayer()->getName()]))
            unset(self::$data["lastPacket"][$e->getPlayer()->getName()]);
    }
}