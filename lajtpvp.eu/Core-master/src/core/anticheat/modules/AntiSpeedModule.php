<?php

declare(strict_types=1);

namespace core\anticheat\modules;

use core\anticheat\BaseModule;
use core\anticheat\modules\data\DistPlayerCalculator;
use JetBrains\PhpStorm\Pure;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;

class AntiSpeedModule extends BaseModule {

    #[Pure] public function __construct() {
        parent::__construct("Speed");
        $this->enabled = false;
    }

    /**
     * @param PlayerMoveEvent $e
     * @priority LOWEST
     * @ignoreCancelled true
     */

    public function onMoveSpeed(PlayerMoveEvent $e) : void {

        $player = $e->getPlayer();

        $dist = DistPlayerCalculator::getDist()[$player->getName()] ?? null;

        if(!$dist || $player->getServer()->getTicksPerSecond() < 20)
            return;

        if(count($dist) <= 49)
            return;

        $a = array_filter($dist);

        if(empty($a))
            return;

        $average = array_sum($a)/count($a);

        if($average < 0.75) {
            if($average >= 0.46) {
                if($this->data["suspectAction"][$player->getName()] < 5) {
                    if($this->data["suspectAction"][$e->getPlayer()->getName()] === 0)
                        $this->data["suspectPosition"][$e->getPlayer()->getName()] = $e->getFrom();

                    $this->data["suspectAction"][$player->getName()] += 1;
                }
            } elseif($average <= 0.35)
                $this->data["suspectAction"][$player->getName()] !== 0 ? $this->data["suspectAction"][$player->getName()] -= 1 : null;
        }

        if($this->data["suspectAction"][$player->getName()] >= 5) {
            if(!isset($this->data["notify"][$player->getName()]) || $this->data["notify"][$player->getName()] <= time()) {
                $this->data["notify"][$player->getName()] = time() + 5;
                $this->notifyAdmin($player->getName());
                $this->data["suspectAction"][$player->getName()]--;
            }

            if($this->enabled) {
                if(!$e->getFrom()->floor()->equals($e->getTo()->floor()) && $player->isOnGround())
                    $e->cancel();
            }
        }
    }

    public function onJoinDist(PlayerJoinEvent $e) : void {
        $this->data["suspectAction"][$e->getPlayer()->getName()] = 0;
    }

    public function onQuitDist(PlayerQuitEvent $e) : void {

        if(isset($this->data["suspectPosition"][$e->getPlayer()->getName()]))
            unset($this->data["suspectPosition"][$e->getPlayer()->getName()]);

        if(isset($this->data["suspectAction"][$e->getPlayer()->getName()]))
            unset($this->data["suspectAction"][$e->getPlayer()->getName()]);
    }
}