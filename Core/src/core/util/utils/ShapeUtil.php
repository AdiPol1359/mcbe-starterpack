<?php

namespace core\util\utils;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class ShapeUtil {

    public static function createExcavator(Position $position) : void {

        $level = $position->getLevel();

        $arm = 3;
        $cornerDist = 2;

        $floorCenter = new Vector3($position->x, $position->y - 1, $position->z);

        $startX = $floorCenter->x - $arm;
        $endX = $floorCenter->x + $arm;
        $startZ = $floorCenter->z - $arm;
        $endZ = $floorCenter->z + $arm;

        self::createAirCube($position);

        self::createFloor($position->getLevel(), $startX, $endX, $startZ, $endZ, $position->y - 1);
        self::createFloor($position->getLevel(), $startX + 1, $endX - 1, $startZ + 1, $endZ - 1, $position->y + 3, Block::STONE_BRICK);

        $corner1 = new Vector3($position->x + $cornerDist, $position->y, $position->z + $cornerDist);
        $corner2 = new Vector3($position->x - $cornerDist, $position->y, $position->z - $cornerDist);
        $corner3 = new Vector3($position->x + $cornerDist, $position->y, $position->z - $cornerDist);
        $corner4 = new Vector3($position->x - $cornerDist, $position->y, $position->z + $cornerDist);

        foreach([$corner1, $corner2, $corner3, $corner4] as $corner) {
            for($y = $corner->y; $y <= $corner->y + 2; $y++)
                $level->setBlockIdAt($corner->x, $y, $corner->z, Block::STONE_BRICK);
        }

        $level->setBlockIdAt($position->x, $position->y + 1, $position->z, Block::IRON_BARS);
        $level->setBlockIdAt($position->x, $position->y + 2, $position->z, Block::COBBLESTONE_WALL);
    }

    public static function createAirCube(Position $position) : void {
        $center = new Position($position->x, $position->y + 2, $position->z, $position->getLevel());

        $radiusXZ = 3;
        $radiusY = 3;

        $positions = [];

        for($x = $center->x - $radiusXZ; $x <= $center->x + $radiusXZ; $x++)
            for($y = $center->y - $radiusY; $y <= $center->y + $radiusY; $y++)
                for($z = $center->z - $radiusXZ; $z <= $center->z + $radiusXZ; $z++)
                    $positions[] = new Vector3($x, $y, $z);

        foreach($positions as $pos)
            $center->getLevel()->setBlockIdAt($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ(), Block::AIR);
    }

    public static function createFloor(Level $level, int $startX, int $endX, int $startZ, int $endZ, int $y, int $id = 1) : void {
        $positions = [];

        for($x = $startX; $x <= $endX; $x++)
            for($z = $startZ; $z <= $endZ; $z++)
                $positions[] = new Vector3($x, $y, $z);

        foreach($positions as $pos)
            $level->setBlockIdAt($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ(), $id);
    }
}