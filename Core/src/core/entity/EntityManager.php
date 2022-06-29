<?php

namespace core\entity;

use core\entity\entities\custom\boss\entities\wither\WitherBoss;
use core\entity\entities\custom\boss\entities\wither\WitherSkeleton;
use core\entity\entities\custom\boss\entities\wither\WitherSkull;
use core\entity\entities\custom\CaveSpawn;
use core\entity\entities\mobs\Villager;
use core\entity\entities\object\FallingBlock;
use core\entity\entities\projectile\EnderPearl;
use pocketmine\entity\Entity;

class EntityManager {

    public static function init() : void {

        Entity::registerEntity(EnderPearl::class, false, ['ThrownEnderpearl', 'minecraft:ender_pearl']);
        Entity::registerEntity(Villager::class, true, ['Villager', 'minecraft:villager']);
        Entity::registerEntity(WitherSkull::class, true, ['WitherSkull', 'minecraft:wither_skull']);
        Entity::registerEntity(WitherSkeleton::class, true, ['WitherSkeleton', 'wither_skeleton']);
        Entity::registerEntity(FallingBlock::class, true, ['FallingSand', 'minecraft:falling_block']);
        Entity::registerEntity(WitherBoss::class, true);
        
        Entity::registerEntity(CaveSpawn::class, true);
    }
}