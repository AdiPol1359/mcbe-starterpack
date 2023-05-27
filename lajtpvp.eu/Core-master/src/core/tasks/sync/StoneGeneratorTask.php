<?php

declare(strict_types=1);

namespace core\tasks\sync;

use pocketmine\block\VanillaBlocks;
use pocketmine\world\Position;
use pocketmine\scheduler\Task;

class StoneGeneratorTask extends Task {

    private Position $spawn;

    public function __construct(Position $spawn) {
        $this->spawn = $spawn;
    }

    public function onRun() : void {
        if(!$this->spawn->getWorld())
            return;

        $this->spawn->getWorld()->setBlock($this->spawn, VanillaBlocks::STONE());
    }
}