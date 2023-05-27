<?php

declare(strict_types=1);

namespace core\anticheat\modules;

use core\anticheat\BaseModule;
use core\anticheat\modules\data\AttackPlayerCalculator;
use JetBrains\PhpStorm\Pure;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;

class AntiReachModule extends BaseModule {

    #[Pure] public function __construct() {
        parent::__construct("Reach");
    }

    /**
     * @param EntityDamageEvent $e
     * @priority LOW
     * @ignoreCancelled true
     */

    public function antiReach(EntityDamageEvent $e) : void {
        if(!$e instanceof EntityDamageByEntityEvent || $e->getCause() === $e::CAUSE_PROJECTILE)
            return;

        $entity = $e->getEntity();
        $damager = $e->getDamager();

        if(!$entity instanceof Player || !$damager instanceof Player)
            return;

        if($damager->isCreative())
            return;

        $dist = AttackPlayerCalculator::getDist()[$damager->getName()] ?? null;

        if(!$dist)
            return;

        if(count($dist) <= 4)
            return;

        $a = array_filter($dist);

        if(empty($a))
            return;

        $average = array_sum($a)/count($a);

        if($average >= 3.6) {
            if($this->data["suspectAction"][$damager->getName()] < 5) {
                $this->data["suspectAction"][$damager->getName()] += 1;
            }

            if($this->isModuleEnabled())
                $e->cancel();
        } else
            $this->data["suspectAction"][$damager->getName()] -= 1;

        if($this->data["suspectAction"][$damager->getName()] >= 5) {
            $this->data["suspectAction"][$damager->getName()] = 0;
            $this->notifyAdmin($damager->getName(), "§c".round($average, 2));
        }
    }

    public function onJoinDist(PlayerJoinEvent $e) : void {
        $this->data["suspectAction"][$e->getPlayer()->getName()] = 0;
    }

    public function onQuitDist(PlayerQuitEvent $e) : void {
        if(isset($this->data["suspectAction"][$e->getPlayer()->getName()]))
            unset($this->data["suspectAction"][$e->getPlayer()->getName()]);
    }
}