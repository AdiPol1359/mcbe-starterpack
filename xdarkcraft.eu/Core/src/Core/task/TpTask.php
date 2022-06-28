<?php

namespace Core\task;

use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level\Position;
use Core\Main;

class TpTask extends Task {

    private $player;

    public function __construct(Player $player, Position $pos) {
        $this->player = $player;
        $this->pos = $pos;
    }

    public function onRun(int $currentTick) {

        $player = $this->player;

        unset(Main::$tpTask[$player->getName()]);

        if(Server::getInstance()->getPlayerExact($player->getName())) {

            $player->teleport($this->pos);

            $player->sendMessage(Main::format("Teleportacja zostala zakonczona pomyslnie"));
        }
    }
}