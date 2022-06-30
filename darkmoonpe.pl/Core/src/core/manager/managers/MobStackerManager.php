<?php

namespace core\manager\managers;

use core\entity\entities\custom\CaveSpawn;
use core\entity\entities\mobs\Villager;
use core\manager\BaseManager;
use core\util\utils\ConfigUtil;
use pocketmine\entity\Animal;
use pocketmine\entity\object\ArmorStand;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;

class MobStackerManager extends BaseManager {

    private Animal $entity;

    private const STACK = 'stack';
    private const LIMIT = 20;

    public function __construct(Animal $entity){
        $this->entity = $entity;
    }

    public function getStackAmount(): int{
        return $this->entity->namedtag->getInt(self::STACK);
    }

    public function isStacked(): bool{
        return $this->entity->namedtag->hasTag(self::STACK);
    }

    public function Stack(): void{

        if($this->isStacked()){
            $this->updateNameTag();
            return;
        }

        if(($mob = $this->findNearStack()) == null){
            $nbt = new IntTag(self::STACK,1);
            $this->entity->namedtag->setTag($nbt);
            $mobstack = $this;
        }else{
            $this->entity->flagForDespawn();
            $mobstack = new MobStackerManager($mob);
            $count = $mob->namedtag->getInt(self::STACK);

            if($count < self::LIMIT)
                $mob->namedtag->setInt(self::STACK, ++$count);
            else
                $mob->namedtag->setInt(self::STACK, 20);
        }

        $mobstack->updateNameTag();
    }

    public function getNamedTag() : CompoundTag{
        return $this->entity->namedtag;
    }

    public function updateNameTag(): void{
        $nbt = $this->entity->namedtag;
        $this->entity->setNameTagAlwaysVisible(True);
        $this->entity->setNameTag("§7x§9§l".$nbt->getInt(self::STACK));
    }

    public function removeStack(): bool{
        $entity = $this->entity;

        $nbt = $entity->namedtag;

        if(!$this->isStacked() || ($c = $this->getStackAmount()) <= 1)
            return false;

        $nbt->setInt(self::STACK,--$c);
        $this->updateNameTag();

        $event = new EntityDeathEvent($entity, $drops = $entity->getDrops());
        $event->call();
        $this->updateNameTag();
        $entity->setHealth($entity->getMaxHealth());

        foreach($drops as $drop)
            $entity->getLevel()->dropItem($entity->getPosition(),$drop);

        return true;
    }

    public function findNearStack(int $range = 12): ?Animal{
        $entity = $this->entity;
        if ($entity->isFlaggedForDespawn() or $entity->isClosed()) return null;
        $boundingbox = $entity->getBoundingBox()->expandedCopy($range, $range, $range);
        foreach ($entity->getLevel()->getNearbyEntities($boundingbox) as $e) {
            if (!$e instanceof Player && $e instanceof Animal){
                if ($e->distance($entity) <= $range&&$e->getName() == $entity->getName()) {
                    $ae = new MobStackerManager($e);
                    if ($ae->isStacked() && !$this->isStacked()) return $e;
                }
            }
        }
        return null;
    }
}