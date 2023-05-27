<?php

declare(strict_types=1);

namespace core\listeners\block;

use core\Main;
use core\utils\Settings;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;

class BlockPlaceListener implements Listener {

    /**
     * @param BlockPlaceEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function protectBlockPlace(BlockPlaceEvent $e) : void {
        $block = $e->getBlock();
        $player = $e->getPlayer();

        if($player->getServer()->isOp($player->getName())) {
            return;
        }

        if(($terrain = Main::getInstance()->getTerrainManager()->getPriorityTerrain($block->getPosition())) !== null){
            if(!$terrain->isSettingEnabled(Settings::$TERRAIN_PLACE_BLOCK))
                $e->cancel();
        }
    }

    public function safePlace(BlockPlaceEvent $e) : void {
        $item = $e->getItem();

        if(Main::getInstance()->getSafeManager()->isSafe($item)) {
            $e->cancel();
        }

        if($item->getId() === VanillaBlocks::SHULKER_BOX()->asItem() || $item->getId() === VanillaBlocks::DYED_SHULKER_BOX()->asItem()) {
            $e->cancel();
        }
    }
}