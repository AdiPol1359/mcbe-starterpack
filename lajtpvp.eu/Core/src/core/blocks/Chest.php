<?php

declare(strict_types=1);

namespace core\blocks;

use core\Main;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier as BID;
use pocketmine\block\BlockLegacyIds as Ids;
use pocketmine\block\BlockToolType;
use pocketmine\block\Chest as PMChest;
use pocketmine\math\Facing;
use pocketmine\block\tile\Chest as TileChest;

class Chest extends PMChest {

    public function __construct() {
        parent::__construct(new BID(Ids::CHEST, 0, null, TileChest::class), "Chest", new BlockBreakInfo(2.5, BlockToolType::AXE));
    }

    public function onPostPlace() : void{
        $tile = $this->position->getWorld()->getTile($this->position);
        if($tile instanceof TileChest){
            foreach([
                        Facing::rotateY($this->facing, true),
                        Facing::rotateY($this->facing, false)
                    ] as $side){
                $c = $this->getSide($side);
                if($c instanceof PMChest and $c->isSameType($this) and $c->facing === $this->facing){
                    $pair = $this->position->getWorld()->getTile($c->position);
                    if($pair instanceof TileChest and !$pair->isPaired()){
                        if(($chestLocker = Main::getInstance()->getChestLockerManager()->getLocker($pair->getPosition()))){
                            break;
                        }

                        $pair->pairWith($tile);
                        $tile->pairWith($pair);
                        break;
                    }
                }
            }
        }
    }
}