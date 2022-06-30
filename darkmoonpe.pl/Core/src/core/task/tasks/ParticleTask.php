<?php

namespace core\task\tasks;

use core\manager\managers\particle\ParticleManager;
use core\util\utils\ConfigUtil;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class ParticleTask extends Task {

    public function onRun(int $currentTick) {

        foreach(ParticleManager::getParticles() as $particle) {

            if($particle->onMove())
                continue;

            foreach($particle->getPlayers() as $playerName) {

                if(!($player = Server::getInstance()->getPlayerExact($playerName)))
                    continue;

                if($player->getLevel()->getName() === ConfigUtil::PVP_WORLD || $player->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
                    continue;

                $particle->onSpawn($player);
            }
        }
    }
}