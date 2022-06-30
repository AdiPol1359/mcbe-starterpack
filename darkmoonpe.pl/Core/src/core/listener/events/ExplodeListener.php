<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\manager\managers\privatechest\ChestManager;
use core\util\utils\ConfigUtil;
use pocketmine\block\Block;
use pocketmine\block\TNT;
use pocketmine\event\entity\EntityExplodeEvent;

class ExplodeListener extends BaseListener{
    public function onExplosion(EntityExplodeEvent $e) : void {
        $blocks = $e->getBlockList();
        $tnt = 0;

        foreach($blocks as $num => $block) {

            if($block->x >= ConfigUtil::CAVE_BORDER || $block->x <= -ConfigUtil::CAVE_BORDER || $block->z >= ConfigUtil::CAVE_BORDER || $block->z <= -ConfigUtil::CAVE_BORDER)
                unset($blocks[$num]);

            if($block instanceof TNT) {

                $block->getLevel()->setBlock($block, Block::get(Block::AIR));
                if($tnt >= 2)
                    unset($blocks[$num]);
                elseif($this->getServer()->getTicksPerSecond() >= 18)
                    $tnt++;
                else
                    unset($blocks[$num]);
            }
        }
        $e->setBlockList($blocks);
    }

    /**
     * @param EntityExplodeEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function onExplodePrivateChest(EntityExplodeEvent $e) : void {
        $blocks = $e->getBlockList();

        foreach($blocks as $num => $block) {
            if($block->getId() !== Block::CHEST)
                continue;

            if(!ChestManager::isLocked($block->asPosition()))
                continue;

            unset($blocks[$num]);
        }
        $e->setBlockList($blocks);
    }
}