<?php

declare(strict_types=1);

namespace Gildie\task;

use Gildie\fakeinventory\SkarbiecInventory;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use Gildie\Main;
use pocketmine\Server;

class UpdateSkarbiecTask extends Task {

    private $player;
    private $inv;

    public function __construct(Player $player, SkarbiecInventory $inv) {
        $this->player = $player;
        $this->inv = $inv;
    }

    public function onRun(int $currentTick) {
        $this->inv->getGuild()->saveSkarbiecItems($this->inv);
        $this->inv->onTransaction($this->player, null);
        $this->inv->getGuild()->setCanSkarbiecTransaction($this->player, true);
    }
}