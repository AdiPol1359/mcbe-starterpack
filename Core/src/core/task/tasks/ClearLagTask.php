<?php

namespace core\task\tasks;

use core\entity\entities\custom\boss\Boss;
use core\entity\entities\custom\boss\entities\wither\WitherSkeleton;
use core\entity\entities\custom\boss\entities\wither\WitherSkull;
use core\entity\entities\mobs\Villager;
use pocketmine\entity\Animal;
use pocketmine\entity\Creature;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\entity\projectile\Throwable;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class ClearLagTask extends Task {

    private int $time;

    public function __construct(int $time) {
        $this->time = time() + $time;
    }

    public function onRun(int $currentTick) {

        if(count(Server::getInstance()->getOnlinePlayers()) <= 2)
            return;

        if($this->time > time() + 60)
            return;

        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            if($this->time === time() + 60)
                $player->sendTip("§7Czyszczenie itemow z ziemi odbedzie sie za §l§9" . abs($this->time - time()) . " §r§7sekund!");

            if($this->time === time() + 30)
                $player->sendTip("§7Czyszczenie itemow z ziemi odbedzie sie za §l§9" . abs($this->time - time()) . " §r§7sekund!");

            if($this->time === time() + 15)
                $player->sendTip("§7Czyszczenie itemow z ziemi odbedzie sie za §l§9" . abs($this->time - time()) . " §r§7sekund!");

            if($this->time <= time() + 5 && $this->time > time())
                $player->sendTip("§7Czyszczenie itemow z ziemi odbedzie sie za §l§9" . abs($this->time - time()) . " §r§7sekund!");

            if($this->time <= time()) {
                $count = 0;

                foreach(Server::getInstance()->getLevels() as $level) {
                    foreach($level->getEntities() as $entity) {
                        if(!$entity instanceof Creature && !$entity instanceof Villager && !$entity instanceof Animal && !$entity instanceof WitherSkull && !$entity instanceof Boss && !$entity instanceof WitherSkeleton && !$entity instanceof Throwable) {
                            $entity->close();

                            $count++;
                        }
                    }
                }

                $this->time = time() + 60*10;
                $player->sendTip("§7Poprawnie wyczyszczono §l§9" . $count . "§r§7 itemow ze swiata");
                return;
            }
        }
    }
}