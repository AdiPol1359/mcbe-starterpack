<?php

declare(strict_types=1);

namespace core\listeners\block;

use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\Listener;

class BlockDecayListener implements Listener {

    public function leaves(LeavesDecayEvent $e) : void {
        $e->cancel();
    }
}