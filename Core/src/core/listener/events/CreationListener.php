<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\user\CorePlayer;
use pocketmine\event\player\PlayerCreationEvent;

class CreationListener extends BaseListener{

    public function registerPlayerClass(PlayerCreationEvent $e) : void{
        $e->setPlayerClass(CorePlayer::class);
    }
}