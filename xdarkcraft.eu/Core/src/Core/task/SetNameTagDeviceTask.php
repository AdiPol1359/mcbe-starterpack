<?php

namespace Core\task;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use Core\api\NameTagsAPI;

class SetNameTagDeviceTask extends Task {

    private $player;
    private $device;

    public function __construct(Player $player, int $device) {
        $this->player = $player;
        $this->device = $device;
    }

    public function onRun(int $currentTick) {
        NameTagsAPI::setDevice($this->player, $this->device);
    }
}