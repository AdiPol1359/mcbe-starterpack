<?php

declare(strict_types=1);

namespace core\anticheat\modules;

use core\anticheat\BaseModule;
use core\Main;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\Transparent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;

class AntiNoclipModule extends BaseModule {

    #[Pure] public function __construct() {
        parent::__construct("Noclip");
    }

    public function AntiCheatOnJoin(PlayerJoinEvent $e){
        $this->data[$e->getPlayer()->getName()] = 0;
    }

    public function AntiCheatNoclip(PlayerMoveEvent $e){

        if($e->getFrom()->floor()->equals($e->getTo()->floor()))
            return;

        $player = $e->getPlayer();

        if($player->isSpectator())
            return;

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());
        $to = $e->getTo();

        $b1 = $e->getTo()->getWorld()->getBlock($to);
        $b2 = $e->getTo()->getWorld()->getBlock($to->add(0, 1, 0));

        if($b1 instanceof Transparent || $b2 instanceof Transparent)
            return;

        if($user) {
            foreach([$b1, $b2] as $checkPos) {
                if($user->isLastEnderPearlPosition($checkPos->getPosition())) {
                    return;
                }
            }
        }

        if($this->enabled)
            $e->cancel();

        $this->data[$e->getPlayer()->getName()]++;
        if($this->data[$e->getPlayer()->getName()] >= 10){
            if(!$e->getFrom()->floor()->equals($e->getTo()))
                $this->notifyAdmin($player->getName());

            $this->data[$e->getPlayer()->getName()] = 0;
        }
    }

    public function onQuit(PlayerQuitEvent $e) : void {
        if(isset($this->data[$e->getPlayer()->getName()]))
            unset($this->data[$e->getPlayer()->getName()]);
    }
}