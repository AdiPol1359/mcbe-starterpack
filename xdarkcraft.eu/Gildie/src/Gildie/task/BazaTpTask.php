<?php

namespace Gildie\task;

use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\math\Vector3;
use Gildie\Main;

class BazaTpTask extends Task {

    private $player;
    private $pos;

    public function __construct(Player $player, Vector3 $pos) {
        $this->player = $player;
        $this->pos = $pos;
    }

    public function onRun(int $currentTick) {
        $player = $this->player;

        unset(Main::$bazaTask[$player->getName()]);

        if(Server::getInstance()->getPlayerExact($player->getName())) {
            $player->teleport($this->pos);
            $player->sendMessage(Main::format("Przeteleportowano do bazy gildii"));
        }
    }
}