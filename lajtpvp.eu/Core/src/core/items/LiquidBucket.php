<?php

declare(strict_types=1);

namespace core\items;

use core\Main;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\item\ItemUseResult;
use pocketmine\item\LiquidBucket as PMLiquidBucket;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class LiquidBucket extends PMLiquidBucket {

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : ItemUseResult {
        if(!$blockReplace->canBeReplaced()){
            return ItemUseResult::NONE();
        }

        $resultBlock = clone $this->getLiquid();

        $ev = new PlayerBucketEmptyEvent($player, $blockReplace, $face, $this, VanillaItems::BUCKET());
        $ev->call();
        if(!$ev->isCancelled()){

            if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($blockReplace->getPosition())) !== null) {
                if($player) {
                    if(!$guild->existsPlayer($player->getName())) {
                        Main::getInstance()->getWaterManager()->addWater($blockReplace->getPosition());
                        $blockReplacePosition = clone $blockReplace->getPosition();

                        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use (&$blockReplacePosition) : void {
                            if($blockReplacePosition->getWorld() === null)
                                return;

                            $blockId = $blockReplacePosition->getWorld()->getBlockAt($blockReplacePosition->x, $blockReplacePosition->y, $blockReplacePosition->z)->getId();
                            if($blockId === BlockLegacyIds::FLOWING_WATER || $blockId === BlockLegacyIds::STILL_WATER) {
                                $blockReplacePosition->getWorld()->setBlock($blockReplacePosition, VanillaBlocks::AIR());
                            }
                        }), 20 * 5);
                    }
                }
            }

            if(($terrain = Main::getInstance()->getTerrainManager()->getPriorityTerrain($blockReplace->getPosition()))) {
                if($player) {
                    Main::getInstance()->getWaterManager()->addWater($blockReplace->getPosition());
                    $blockReplacePosition = clone $blockReplace->getPosition();

                    Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use (&$blockReplacePosition) : void {
                        if($blockReplacePosition->getWorld() === null)
                            return;

                        $blockId = $blockReplacePosition->getWorld()->getBlockAt($blockReplacePosition->x, $blockReplacePosition->y, $blockReplacePosition->z)->getId();
                        if($blockId === BlockLegacyIds::FLOWING_WATER || $blockId === BlockLegacyIds::STILL_WATER) {
                            $blockReplacePosition->getWorld()->setBlock($blockReplacePosition, VanillaBlocks::AIR());
                        }
                    }), 20 * 30);
                    //TODO: zrobić jednego taska który by usuwał po 30 sekundach
                }
            }


            $player->getWorld()->setBlock($blockReplace->getPosition(), $resultBlock->getFlowingForm(), false);
            $player->getWorld()->addSound($blockReplace->getPosition()->add(0.5, 0.5, 0.5), $resultBlock->getBucketEmptySound());

            if($player->hasFiniteResources()){
                $player->getInventory()->setItemInHand($ev->getItem());
            }
            return ItemUseResult::SUCCESS();
        }

        return ItemUseResult::FAIL();
    }
}