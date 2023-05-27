<?php

declare(strict_types=1);

namespace core\tasks\sync;

use core\inventories\fakeinventories\AbyssInventory;
use core\inventories\FakeInventoryManager;
use core\Main;
use core\managers\AbyssManager;
use core\utils\BroadcastUtil;
use core\utils\Settings;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class AbyssTask extends Task {

    private AbyssManager $abyssManager;
    private int $time;

    public function __construct(AbyssManager $abyssManager) {
        $this->time = Settings::$ABYSS_TIME;
        $this->abyssManager = $abyssManager;
    }

    public function onRun() : void {

        $times = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        for($i = 1; $i <= 5; $i++) {
            if($this->time === 60 * $i) {
                $this->message("§7OTWARCIE OTCHLANI §e" . $i . " §7MINUT");
                break;
            }
        }

        if(in_array($this->time, $times))
            $this->message("§7OTWARCIE OTCHLANI §e".$this->time." §7SEKUND");

        if($this->time <= 0) {

            foreach($this->abyssManager->getItemEntities() as $id => $entity) {
                $item = clone $entity->getItem();

                for($i = $item->getCount(); $i > $item->getMaxStackSize(); $i -= $item->getMaxStackSize())
                    $this->abyssManager->addItem(clone $entity->getItem()->setCount($item->getMaxStackSize()));

                $this->abyssManager->addItem(clone $entity->getItem()->setCount($i));
                $entity->close();
            }

            $this->abyssManager->setOpened(true);
            $this->message("§7OTCHLAN ZOSTALA OTWARTA");

            $this->time = Settings::$ABYSS_TIME;
            $time = 20;

            $handler = Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() use (&$handler, &$time) : void {

                if($time <= 0) {
                    $this->abyssManager->setOpened(false);

                    foreach(FakeInventoryManager::getInventories() as $nick => $inventory) {
                        if($inventory instanceof AbyssInventory) {
                            if(($player = Server::getInstance()->getPlayerExact($nick)))
                                $inventory->closeFor($player);
                        }
                    }

                    $this->message("§7OTCHLAN ZOSTALA ZAMKNIETA");
                    $this->abyssManager->clearAll();
                    $handler->cancel();
                    return;
                }

                if($time <= 15)
                    $this->message("§7ZAMKNIECIE OTCHLANI §e".$time." §7SEKUND");

                $time--;
            }), 20);
        }

        $this->time--;
    }

    private function message(string $message) : void {
        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($message) : void {
            $onlinePlayer->sendTip($message);
        });
    }
}