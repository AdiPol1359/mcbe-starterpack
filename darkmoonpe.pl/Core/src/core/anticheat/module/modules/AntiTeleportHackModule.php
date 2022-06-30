<?php

namespace core\anticheat\module\modules;

use core\anticheat\module\BaseModule;
use core\anticheat\module\modules\data\DistPlayerCalculator;
use core\Main;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\scheduler\ClosureTask;

class AntiTeleportHackModule extends BaseModule {

    public function __construct() {
        parent::__construct("TeleportHack");
    }

    /**
     * @param PlayerMoveEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function onMoveSpeed(PlayerMoveEvent $e) : void {

        if(!$this->enabled)
            return;

        $player = $e->getPlayer();

        $dist = DistPlayerCalculator::getDist()[$player->getName()] ?? null;

        if(!$dist)
            return;

        if(count($dist) <= 49)
            return;

        $a = array_filter($dist);

        if(empty($a))
            return;

        $average = array_sum($a)/count($a);

        if($average >= 0.75) {
            if($this->data["suspectAction"][$player->getName()] < 5) {
                if($this->data["suspectAction"][$e->getPlayer()->getName()] === 0)
                    $this->data["suspectPosition"][$e->getPlayer()->getName()] = $e->getFrom();

                $this->data["suspectAction"][$player->getName()] += 1;
            }
        }elseif($average <= 0.43)
            $this->data["suspectAction"][$player->getName()] !== 0 ? $this->data["suspectAction"][$player->getName()] -= 1 : null;

        if($this->data["suspectAction"][$player->getName()] >= 5) {

            if(!isset($this->data["notify"][$player->getName()]) || $this->data["notify"][$player->getName()] <= time()) {
                $this->data["notify"][$player->getName()] = time() + 5;
                $this->notifyAdmin($player->getName());
            }

            Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void{
                if(!$player)
                    return;
                $player->close("", "§l§cWykryto cheaty!");
            }), 3);

            $this->data["suspectPosition"][$e->getPlayer()->getName()] !== null ? $player->teleport($this->data["suspectPosition"][$e->getPlayer()->getName()]) : $e->setCancelled(true);
        }
    }

    public function onJoinDist(PlayerJoinEvent $e) : void {
        $this->data["suspectPosition"][$e->getPlayer()->getName()] = null;
        $this->data["suspectAction"][$e->getPlayer()->getName()] = 0;
    }

    public function onQuitDist(PlayerQuitEvent $e) : void {

        if(isset($this->data["suspectPosition"][$e->getPlayer()->getName()]))
            unset($this->data["suspectPosition"][$e->getPlayer()->getName()]);

        if(isset($this->data["suspectAction"][$e->getPlayer()->getName()]))
            unset($this->data["suspectAction"][$e->getPlayer()->getName()]);
    }
}