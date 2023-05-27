<?php

declare(strict_types=1);

namespace core\blocks;

use core\Main;
use core\utils\Settings;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Farmland as PMFarmland;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\player\Player;

class Farmland extends PMFarmland{

    public function __construct() {
        parent::__construct(
            new BlockIdentifier(BlockLegacyIds::FARMLAND, 0),
            "Farmland",
            new BlockBreakInfo(0.6, BlockToolType::SHOVEL)
        );
    }

    public function onEntityFallenUpon(Entity $entity, float $fallDistance) : void{
        if($entity instanceof Player){
            if(($terrain = Main::getInstance()->getTerrainManager()->getPriorityTerrain($this->getPosition())) === null || $terrain->isSettingEnabled(Settings::$TERRAIN_INTERACT)) {
                $ev = new BlockFormEvent($this, VanillaBlocks::DIRT());
                $ev->call();

                if(!$ev->isCancelled()){
                    $this->getPosition()->getWorld()->setBlock($this->getPosition(), $ev->getNewState(), true);
                }
            }
        }
    }
}