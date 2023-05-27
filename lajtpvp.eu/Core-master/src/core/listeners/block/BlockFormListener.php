<?php

declare(strict_types=1);

namespace core\listeners\block;

use core\Main;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\Listener;

class BlockFormListener implements Listener {

    /**
     * @param BlockFormEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function blockWater(BlockFormEvent $e) : void{
        if($e->isCancelled())
            return;

        $block = $e->getBlock()->getPosition();

        if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($block)) !== null) {
            if($guild->isInHeart($block)) {
                $e->setCancelled(true);
            }
        }
    }
}