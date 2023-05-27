<?php

declare(strict_types=1);

namespace core\tasks\sync;

use core\guilds\Guild;
use core\utils\MessageUtil;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class RegenerationTask extends Task {

    public function __construct(private Guild $guild) {}

    public function onRun() : void {

        if(!$this->guild) {
            $this->getHandler()->cancel();
            return;
        }

        $blocks = $this->guild->getRegenerationBlocks();

        if(empty($blocks)) {
            foreach($this->guild->getPlayers() as $guildPlayer) {
                $player = Server::getInstance()->getPlayerExact($guildPlayer->getName());

                if(!$player)
                    continue;

                $player->sendMessage(MessageUtil::format("Regeneracja twojej gildii zostala zakonczona!"));
            }

            $this->getHandler()->cancel();
            $this->guild->resetRegeneration();
            return;
        }

        if($this->guild->getRegenerationGold() <= 0) {
            foreach($this->guild->getPlayers() as $guildPlayer) {
                $player = Server::getInstance()->getPlayerExact($guildPlayer->getName());

                if(!$player)
                    continue;

                $player->sendMessage(MessageUtil::format("Regeneracja twojej gildii zostala przerwana poniewaz zabraklo zlota!"));
            }

            $this->guild->setRegeneration(false);
            return;
        }

        $firstBlock = $blocks[array_key_first($blocks)];

        Server::getInstance()->getWorldManager()->getDefaultWorld()->setBlock($firstBlock->asVector3(), $firstBlock);
        $this->guild->removeRegenerationBlock();
    }
}