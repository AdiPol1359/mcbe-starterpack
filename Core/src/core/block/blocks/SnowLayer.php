<?php

namespace core\block\blocks;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockToolType;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\TieredTool;
use pocketmine\math\Vector3;
use pocketmine\Player;

class SnowLayer extends Flowable {

    protected $id = self::SNOW_LAYER;

    public function __construct(int $meta = 0) {
        $this->meta = $meta;
    }

    public function getName() : string {
        return "Snow Layer";
    }

    public function canBeReplaced() : bool {
        return $this->meta < 7; //8 snow layers
    }

    public function getHardness() : float {
        return 0.1;
    }

    public function getToolType() : int {
        return BlockToolType::TYPE_SHOVEL;
    }

    public function getToolHarvestLevel() : int {
        return TieredTool::TIER_WOODEN;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool {
        if($blockReplace->getId() === $this->getId()&&$blockReplace->getDamage() < 7) {
            $this->setDamage($blockReplace->getDamage() + 1);
        }
        if($this->canBeSupportedBy($blockReplace->getSide(Vector3::SIDE_DOWN))) {
            $this->getLevelNonNull()->setBlock($blockReplace, $this, true);

            return true;
        }

        return false;
    }

    private function canBeSupportedBy(Block $b) : bool {
        return $b->isSolid() or ($b->getId() === $this->getId()&&$b->getDamage() === 7);
    }

    public function onNearbyBlockChange() : void {
        if(!$this->canBeSupportedBy($this->getSide(Vector3::SIDE_DOWN))) {
            $this->getLevelNonNull()->setBlock($this, BlockFactory::get(Block::AIR), false, false);
        }
    }

    public function ticksRandomly() : bool {
        return true;
    }

    public function onRandomTick() : void {
        if($this->level->getBlockLightAt($this->x, $this->y, $this->z) >= 12) {
            $this->getLevelNonNull()->setBlock($this, BlockFactory::get(Block::AIR), false, false);
        }
    }

    public function getDropsForCompatibleTool(Item $item) : array {
        return [
            ItemFactory::get(Item::SNOWBALL)
        ];
    }

    public function isAffectedBySilkTouch() : bool {
        return false;
    }
}