<?php

declare(strict_types=1);

namespace core\managers;

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\Position;
use pocketmine\Server;

class WaterManager {

    /** @var Position[] */
    private array $water = [];

    public function addWater(Position $position) : void {
        if($this->isDelayedWater($position))
            return;

        $this->water[] = $position;
    }

    public function removeWater(Position $position) : void {
        foreach($this->water as $key => $pos) {
            if($pos->equals($position))
                unset($this->water[$key]);
        }
    }

    public function isDelayedWater(Position $position) : bool {
        foreach($this->water as $key => $pos) {
            if($pos->equals($position))
                return true;
        }

        return false;
    }

    public function getAllDelayedWater() : array {
        return $this->water;
    }

    public function save() : void {
        foreach($this->getAllDelayedWater() as $key => $position) {
            if(($block = Server::getInstance()->getWorldManager()->getDefaultWorld()->getBlock($position))) {
                $position = $block->getPosition();
                if($block->getId() === BlockLegacyIds::WATER || $block->getId() === BlockLegacyIds::FLOWING_WATER) {
                    $position->getWorld()->setBlock($position, VanillaBlocks::AIR());
                }
            }
        }
    }
}