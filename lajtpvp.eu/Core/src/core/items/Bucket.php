<?php

declare(strict_types=1);

namespace core\items;

use core\Main;
use pocketmine\block\Block;
use pocketmine\block\Liquid;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\item\Bucket as PMBucket;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Bucket extends PMBucket {

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : ItemUseResult{

        if($blockClicked instanceof Liquid and $blockClicked->isSource()){
            $stack = clone $this;
            $stack->pop();

            $resultItem = ItemFactory::getInstance()->get(ItemIds::BUCKET, $blockClicked->getFlowingForm()->getId());
            $ev = new PlayerBucketFillEvent($player, $blockReplace, $face, $this, $resultItem);
            $ev->call();
            if(!$ev->isCancelled()){
                Main::getInstance()->getWaterManager()->removeWater($blockClicked->getPosition());
                $player->getWorld()->setBlock($blockClicked->getPosition(), VanillaBlocks::AIR());
                $player->getWorld()->addSound($blockClicked->getPosition()->add(0.5, 0.5, 0.5), $blockClicked->getBucketFillSound());
                if($player->hasFiniteResources()){
                    if($stack->getCount() === 0){
                        $player->getInventory()->setItemInHand($ev->getItem());
                    }else{
                        $player->getInventory()->setItemInHand($stack);
                        $player->getInventory()->addItem($ev->getItem());
                    }
                }else{
                    $player->getInventory()->addItem($ev->getItem());
                }
                return ItemUseResult::SUCCESS();
            }

            return ItemUseResult::FAIL();
        }

        return ItemUseResult::NONE();
    }
}