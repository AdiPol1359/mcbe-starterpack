<?php

declare(strict_types=1);

namespace core\anticheat\modules\data;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;

class AttackPlayerCalculator implements Listener {

    private static array $data = [];

    /**
     * @param EntityDamageEvent $e
     * @priority LOWEST
     * @ignoreCancelled true
     */

    public function reachCheck(EntityDamageEvent $e) : void {
        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        $damager = $e->getDamager();
        $entity = $e->getEntity();

        if($damager === null || $entity === null) {
            return;
        }

        $damagerPosition = $damager->getPosition();
        $entityPosition = $entity->getPosition();

        if(!$entity instanceof Player || !$damager instanceof Player)
            return;

        if($damager->getNetworkSession()->getPing() >= 500)
            return;

        $dist = sqrt(pow($damagerPosition->x - $entityPosition->x, 2) + pow($damagerPosition->z - $entityPosition->z, 2));

        if(isset(self::$data["dist"][$damager->getName()])) {
            if((count(self::$data["dist"][$damager->getName()])) >= 5)
                unset(self::$data["dist"][$damager->getName()][array_key_first(self::$data["dist"][$damager->getName()])]);
        }

        $remove = 0;

        if($damager->getNetworkSession()->getPing() >= 80)
            $remove += ($damager->getNetworkSession()->getPing() / 100);

        if($entityPosition->getFloorY() > $damagerPosition->getFloorY())
            $remove += $entityPosition->getFloorY() * 1.3;

        if(($dist - $remove) < 0)
            return;

        self::$data["dist"][$damager->getName()][] = ($dist - $remove);
    }

    public static function getDist() : array {
        return self::$data["dist"];
    }

    public function onJoinDist(PlayerJoinEvent $e) : void {
        self::$data["dist"][$e->getPlayer()->getName()] = [];
    }

    public function onQuitDist(PlayerQuitEvent $e) : void {
        if(isset(self::$data["dist"][$e->getPlayer()->getName()]))
            unset(self::$data["dist"][$e->getPlayer()->getName()]);
    }
}