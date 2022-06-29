<?php

namespace core\block\blocks;

use core\caveblock\CaveManager;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\Farmland as PMFarmland;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\level\GameRules;
use pocketmine\Player;

class Farmland extends PMFarmland{

    public function onEntityFallenUpon(Entity $entity, float $fallDistance) : void{
        if($entity instanceof Player){

            if(!CaveManager::isInCave($entity))
                return;

            $cave = CaveManager::getCave($entity);

            if(!$cave->isMember($entity->getName()))
                return;

            if($this->level->random->nextFloat() < ($fallDistance - 0.5)){
                $ev = new BlockFormEvent($this, BlockFactory::get(Block::DIRT));

                if(!$this->level->getGameRules()->getBool(GameRules::RULE_MOB_GRIEFING, true)){
                    $ev->setCancelled();
                }
                $ev->call();

                if(!$ev->isCancelled()){
                    $this->level->setBlock($this, $ev->getNewState(), true);
                }
            }
        }
    }
}