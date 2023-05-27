<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\users\CorePlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;

class PlayerCreationListener implements Listener {

    public function playerCreation(PlayerCreationEvent $e) : void {
        $e->setPlayerClass(CorePlayer::class);
    }
}