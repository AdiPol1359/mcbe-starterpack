<?php

declare(strict_types=1);

namespace core\listeners\entity;

use core\Main;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\Listener;
use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class ExplosionPrimeListener implements Listener {

    public function onExplode(ExplosionPrimeEvent $ev): void {
        $level = $ev->getEntity()->getWorld();

        Main::getInstance()->getScheduler()->scheduleTask(new ClosureTask(function () use ($ev, $level): void {
            $src = $ev->getEntity();
            $pos = $src->getPosition();

            $explosionSize = $ev->getForce() * 2;
            $minX = (int)floor($pos->x - $explosionSize - 1);
            $maxX = (int)ceil($pos->x + $explosionSize + 1);
            $minY = (int)floor($pos->y - $explosionSize - 1);
            $maxY = (int)ceil($pos->y + $explosionSize + 1);
            $minZ = (int)floor($pos->z - $explosionSize - 1);
            $maxZ = (int)ceil($pos->z + $explosionSize + 1);

            $explosionBB = new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);

            $list = $level->getNearbyEntities($explosionBB, $src);

            foreach($list as $entity) {
                if($entity instanceof Player)
                    continue;

                $position = clone $entity->getPosition();

                $distance = $position->distance($src->getPosition()) / $explosionSize;

                if($distance <= 1) {
                    $motion = $position->subtract($pos->x, $pos->y, $pos->z)->normalize();
                    $impact = (1 - $distance) * ($exposure = 1);

                    $entity->setMotion($motion->multiply($impact)->multiply(11));
                }
            }
        }));
    }
}