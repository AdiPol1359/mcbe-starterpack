<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\util\utils\ConfigUtil;
use pocketmine\event\player\PlayerBucketFillEvent;

class BucketFillListener extends BaseListener{

    /**
     * @param PlayerBucketFillEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function onBucketFillEInSpawn(PlayerBucketFillEvent $e) : void{
        if($e->getPlayer()->getLevel()->getName() === ConfigUtil::DEFAULT_WORLD && $e->getPlayer()->isSurvival()) {
            $e->setCancelled(true);
            $e->getPlayer()->setGamemode(2);
        }
    }
}