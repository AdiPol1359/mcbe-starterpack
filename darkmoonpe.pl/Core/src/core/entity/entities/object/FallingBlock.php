<?php

namespace core\entity\entities\object;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\Fallable;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Position;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\IntTag;
use UnexpectedValueException;

class FallingBlock extends Entity {

    public const NETWORK_ID = self::FALLING_BLOCK;

    public $width = 0.98;
    public $height = 0.98;
    public $canCollide = false;
    protected $baseOffset = 0.49;
    protected $gravity = 0.04;
    protected $drag = 0.02;
    /** @var Block */
    protected $block;

    public function canCollideWith(Entity $entity) : bool {
        return false;
    }

    public function canBeMovedByCurrents() : bool {
        return false;
    }

    public function attack(EntityDamageEvent $source) : void {
        if($source->getCause() === EntityDamageEvent::CAUSE_VOID) {
            parent::attack($source);
        }
    }

    public function entityBaseTick(int $tickDiff = 1) : bool {
        if($this->closed) {
            return false;
        }

        $hasUpdate = parent::entityBaseTick($tickDiff);

        if(!$this->isFlaggedForDespawn()) {
            $pos = Position::fromObject($this->add(-$this->width / 2, $this->height, -$this->width / 2)->floor(), $this->getLevelNonNull());

            $this->block->position($pos);

            $blockTarget = null;
            if($this->block instanceof Fallable) {
                $blockTarget = $this->block->tickFalling();
            }

            if($this->onGround) {
                $this->flagForDespawn();

                $block = $this->level->getBlock($pos);
                $ev = new EntityBlockChangeEvent($this, $block, $blockTarget ?? $this->block);
                $ev->call();
                if(!$ev->isCancelled()) {
                    $this->getLevelNonNull()->setBlock($pos, $ev->getTo(), true);
                }
                $hasUpdate = true;
            }
        }

        return $hasUpdate;
    }

    public function getBlock() : int {
        return $this->block->getId();
    }

    public function getDamage() : int {
        return $this->block->getDamage();
    }

    public function saveNBT() : void {
        parent::saveNBT();
        $this->namedtag->setInt("TileID", $this->block->getId(), true);
        $this->namedtag->setByte("Data", $this->block->getDamage());
    }

    protected function initEntity() : void {
        parent::initEntity();

        $blockId = 0;

        if($this->namedtag->hasTag("TileID", IntTag::class)) {
            $blockId = $this->namedtag->getInt("TileID");
        } elseif($this->namedtag->hasTag("Tile", ByteTag::class)) {
            $blockId = $this->namedtag->getByte("Tile");
            $this->namedtag->removeTag("Tile");
        }

        if($blockId === 0) {
            throw new UnexpectedValueException("Invalid " . get_class($this) . " entity: block ID is 0 or missing");
        }

        $damage = $this->namedtag->getByte("Data", 0);

        $this->block = BlockFactory::get($blockId, $damage);

        $this->propertyManager->setInt(self::DATA_VARIANT, $this->block->getRuntimeId());
    }
}