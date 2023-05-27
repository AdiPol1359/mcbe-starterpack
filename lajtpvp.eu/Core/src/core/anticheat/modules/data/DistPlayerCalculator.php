<?php

declare(strict_types=1);

namespace core\anticheat\modules\data;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class DistPlayerCalculator implements Listener {

    private static array $data = [];
    
    /**
     * @param PlayerMoveEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function onMoveSpeed(PlayerMoveEvent $e) : void {

        if($e->isCancelled())
            return;

        $from = $e->getFrom();
        $to = $e->getTo();
        $player = $e->getPlayer();

        if($player->getNetworkSession()->getPing() >= 350 || $player->getServer()->getTicksPerSecond() < 20)
            return;

//        if(!($motion = $player->getMotion())->equals(new Vector3()))
//            $to->subtract($motion);

        if($player->isCreative() || $player->isSpectator() || $player->isFlying() && !$e->getFrom()->floor()->equals($e->getTo()->floor()))
            self::$data["lastFlyTick"][$player->getName()] = Server::getInstance()->getTick();

        if($player->isFlying() || $player->fallDistance >= 2)
            return;

        if(isset(self::$data["lastFlyTick"][$player->getName()]) && (self::$data["lastFlyTick"][$player->getName()] + 40) > Server::getInstance()->getTick())
            return;

        $dist = sqrt(pow($from->x - $to->x, 2) + pow($from->z - $to->z, 2));

        if(isset(self::$data["dist"][$player->getName()])) {
            if((count(self::$data["dist"][$player->getName()])) >= 50)
                unset(self::$data["dist"][$player->getName()][array_key_first(self::$data["dist"][$player->getName()])]);
        }

        if(isset(self::$data["packetTime"][$player->getName()])) {
            if((count(self::$data["packetTime"][$player->getName()])) >= 50)
                unset(self::$data["packetTime"][$player->getName()][array_key_first(self::$data["packetTime"][$player->getName()])]);
        }

        if(!$e->getFrom()->asVector3()->equals($e->getTo()->asVector3())) {

            $remove = 0;

            if(($block = $player->getWorld()->getBlock($player->getPosition()->add(0, 2, 0)))) {
                if($block->getId() !== VanillaBlocks::AIR())
                    $remove += 0.1;
            }
            foreach($player->getEffects() as $effectInstance) {
                if($effectInstance->getId() === VanillaEffects::SPEED()) {
                    $remove += (($effectInstance->getAmplifier() + 1) * 2) / 100;
                }
            }

            if($player->fallDistance >= 5)
                $remove += ($player->fallDistance / 10);

            if($player->getNetworkSession()->getPing() >= 80)
                $remove += ($player->getNetworkSession()->getPing() / 2500);

            $b = array_filter(self::$data["packetTime"][$player->getName()]);

            if(!empty($b)) {
                $averagePacket = (microtime(true) - (array_sum($b) / count($b)));
                if($averagePacket > 1.4) {
                    if($averagePacket <= 5)
                        $remove += ($averagePacket / 14);
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

    public function onTeleport(EntityTeleportEvent $e) : void {
        $entity = $e->getEntity();

        if(!$entity instanceof Player)
            return;

        self::$data["dist"][$entity->getName()] = [];
        self::$data["packetTime"][$entity->getName()] = [];
        self::$data["lastPacket"][$entity->getName()] = 0;
    }

    public static function getData() : array {
        return self::$data;
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