<?php

namespace core\listener\events;

use core\caveblock\CaveManager;
use core\fakeinventory\inventory\InvSeeInventory;
use core\listener\BaseListener;
use core\Main;
use core\util\utils\MessageUtil;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\scheduler\ClosureTask;

class InventoryPickupListener extends BaseListener{

    private array $pickUp = [];

    public function InventoryPickupCave(InventoryPickupItemEvent $e) {
        $player = $e->getInventory()->getHolder();

        if($player->isOp())
            return;

        if(!CaveManager::isInCave($player))
            return;

        $cave = CaveManager::getCave($player);
        if($cave->isOwner($player->getName()))
            return;

        if(!$cave->isMember($player->getName())){
            if(!in_array($player->getName(), $this->pickUp)){

                $player->sendMessage(MessageUtil::format("Nie mozesz podnies itemu poniewaz nie masz uprawnien!"));
                $this->pickUp[] = $player->getName();

                Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void{
                    if (($key = array_search($player->getName(), $this->pickUp)) !== false)
                        unset($this->pickUp[$key]);
                }), 20*5);
            }
            $e->setCancelled(true);
            return;
        }

        if($cave->getPlayerSetting($player->getName(), "p_item"))
            return;

        if(!in_array($player->getName(), $this->pickUp)){

            $player->sendMessage(MessageUtil::format("Nie mozesz podnies itemu poniewaz nie masz uprawnien!"));
            $this->pickUp[] = $player->getName();

            Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void{
                if (($key = array_search($player->getName(), $this->pickUp)) !== false)
                    unset($this->pickUp[$key]);
            }), 20*5);
        }
        $e->setCancelled(true);
    }

    /**
     * @param InventoryPickupItemEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function invSeeTransaction(InventoryPickupItemEvent $e) : void{
        $player = $e->getInventory()->getHolder();

        foreach(Main::$invSeePlayers as $nick => $inv){
            if(!$inv instanceof InvSeeInventory)
                continue;

            if($player->getName() === $nick)
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($inv) : void{
                    $inv->setItems();
                }), 1);
        }
    }
}