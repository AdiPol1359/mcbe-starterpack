<?php

namespace core\listener\events;

use core\listener\BaseListener;
use pocketmine\event\player\PlayerToggleSwimEvent;

class ToggleSwimListener extends BaseListener {

    public function disableSwim(PlayerToggleSwimEvent $e) : void {
        $e->setCancelled(true);
    }
}