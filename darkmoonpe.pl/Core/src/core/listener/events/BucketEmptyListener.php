<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\util\utils\ConfigUtil;
use pocketmine\event\player\PlayerBucketEmptyEvent;

class BucketEmptyListener extends BaseListener{

    /**
     * @param PlayerBucketEmptyEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function EmptyBucketInSpawn(PlayerBucketEmptyEvent $e) : void{

        if($e->getPlayer()->getLevel()->getName() === ConfigUtil::DEFAULT_WORLD && $e->getPlayer()->isSurvival()) {
            $e->setCancelled(true);
            $e->getPlayer()->setGamemode(2);
        }
    }
}