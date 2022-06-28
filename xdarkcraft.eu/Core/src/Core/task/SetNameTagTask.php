<?php

namespace Core\task;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use Core\api\NameTagsAPI;

class SetNameTagTask extends Task {

    private $player;

    public function __construct(Player $player) {
        $this->player = $player;
    }

    public function onRun(int $currentTick) {
        NameTagsAPI::setNameTag($this->player);
    }
}