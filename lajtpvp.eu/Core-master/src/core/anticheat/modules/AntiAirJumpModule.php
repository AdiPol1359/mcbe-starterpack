<?php

declare(strict_types=1);

namespace core\anticheat\modules;

use core\anticheat\BaseModule;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\BlockLegacyIds;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector3;

class AntiAirJumpModule extends BaseModule {

    #[Pure] public function __construct() {
        parent::__construct("AirJump");
    }

    public function playerJump(PlayerJumpEvent $e) : void {
        $player = $e->getPlayer();
        $playerPosition = $player->getPosition();

        $blockUnderPlayer = 0;

        for($i = $playerPosition->getFloorY(); $playerPosition->getWorld()->getBlockAt($playerPosition->getFloorX(), $i, $playerPosition->getFloorZ())->getId() === BlockLegacyIds::AIR; $i--) {
            if($i < 0)
                break;

            $blockUnderPlayer = $i;
        }

        if($player->getServer()->getTicksPerSecond() < 19 || $player->getNetworkSession()->getPing() > 350 || $player->isOnGround() || $player->getPosition()->distance(new Vector3($player->getPosition()->getFloorX(), $blockUnderPlayer, $player->getPosition()->getFloorZ())) <= 1)
            return;

        if(!isset($this->data[$player->getName()]))
            $this->data[$player->getName()] = 0;

        $this->data[$player->getName()]++;

        if($this->data[$player->getName()] > 2)
            $this->notifyAdmin($player->getName());

//        if($this->data[$player->getName()] > 5) {
//            if($this->isModuleEnabled())
//                $player->close("", "§l§cWykryto cheaty!");
//        }
    }

    public function move(PlayerMoveEvent $e) : void {
        if($e->getFrom()->floor()->equals($e->getTo()->floor()))
            return;

        $player = $e->getPlayer();

        if($player->getNetworkSession()->getPing() > 350 || !$player->isOnGround())
            return;

        if(!isset($this->data[$player->getName()]))
            $this->data[$player->getName()] = 0;

        if($this->data[$player->getName()] > 0)
            $this->data[$player->getName()]--;
    }

    public function AntiCheatOnJoin(PlayerJoinEvent $e){
        $this->data[$e->getPlayer()->getName()] = 0;
    }

    public function onQuit(PlayerQuitEvent $e) : void {
        if(isset($this->data[$e->getPlayer()->getName()]))
            unset($this->data[$e->getPlayer()->getName()]);
    }
}