<?php

namespace core\block\blocks;

use core\generator\object\Tree as FixedTree;
use pocketmine\block\Block;
use pocketmine\block\Sapling as Sap;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\Random;

class Sapling extends Sap {

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool {
        $down = $this->getSide(Vector3::SIDE_DOWN);
        if($down->getId() === self::GRASS || $down->getId() === self::DIRT || $down->getId() === self::FARMLAND) {
            $this->getLevel()->setBlock($blockReplace, $this, true, true);

            return true;
        }

        return false;
    }

    public function onActivate(Item $item, Player $player = null) : bool {
        if($item->getId() === Item::DYE && $item->getDamage() === 0x0F) {
            FixedTree::growTree($this->getLevel(), $this->x, $this->y, $this->z, new Random(mt_rand()), $this->getVariant());

            $item->count--;

            return true;
        }

        return false;
    }

    public function onNearbyBlockChange() : void {
        if($this->getSide(Vector3::SIDE_DOWN)->isTransparent()) {
            $this->getLevel()->useBreakOn($this);
        }
    }

    public function ticksRandomly() : bool {
        return true;
    }

    public function onRandomTick() : void {
        if(mt_rand(1, 3) === 1) {
            if(($this->meta & 0x08) === 0x08) {
                FixedTree::growTree($this->getLevel(), $this->x, $this->y, $this->z, new Random(mt_rand()), $this->getVariant());
            } else {
                $this->meta |= 0x08;
                $this->getLevel()->setBlock($this, $this, true);
            }
        }
    }

    public function getVariantBitmask() : int {
        return 0x07;
    }

    public function getFuelTime() : int {
        return 100;
    }
}