<?php

declare(strict_types=1);

namespace core\listeners\player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;

class PlayerDeathListener implements Listener {

    public function deathMessage(PlayerDeathEvent $e) : void {
        $e->setDeathMessage("");
    }
}