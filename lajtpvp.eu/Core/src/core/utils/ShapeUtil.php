<?php

declare(strict_types=1);

namespace core\utils;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\math\Vector3;

final class ShapeUtil {

    private function __construct() {}

    public static function createGuildShape(Position $heartPosition) : void {
        self::createAirCube($heartPosition);

        $arm = 3;

        $floorCenter = new Vector3($heartPosition->x, $heartPosition->y - 1, $heartPosition->z);

        $startX = $floorCenter->x - $arm;
        $endX = $floorCenter->x + $arm;
        $startZ = $floorCenter->z - $arm;
        $endZ = $floorCenter->z + $arm;

        self::createFloor($heartPosition->getWorld(), $startX, $endX, $startZ, $endZ, $heartPosition->y - 1);
        self::createFloor($heartPosition->getWorld(), $startX, $endX, $startZ, $endZ, $heartPosition->y + 4);
        self::createStructure($heartPosition);

        $corner1 = new Vector3($heartPosition->x + $arm, $heartPosition->y, $heartPosition->z + $arm);
        $corner2 = new Vector3($heartPosition->x - $arm, $heartPosition->y, $heartPosition->z - $arm);
        $corner3 = new Vector3($heartPosition->x + $arm, $heartPosition->y, $heartPosition->z - $arm);
        $corner4 = new Vector3($heartPosition->x - $arm, $heartPosition->y, $heartPosition->z + $arm);

        foreach([$corner1, $corner2, $corner3, $corner4] as $corner) {
            for($y = $corner->y; $y <= $corner->y + 3; $y++)
                $heartPosition->getWorld()->setBlockAt($corner->x, $y, $corner->z, VanillaBlocks::OBSIDIAN());
        }
    }

    public static function createAirCube(Position $heartPosition) : void {
        $center = new Position($heartPosition->x, $heartPosition->y + 2, $heartPosition->z, $heartPosition->getWorld());

        $radiusXZ = 4;
        $radiusY = 3;

        $positions = [];

        for($x = $center->x - $radiusXZ; $x <= $center->x + $radiusXZ; $x++)
            for($y = $center->y - $radiusY; $y <= $center->y + $radiusY; $y++)
                for($z = $center->z - $radiusXZ; $z <= $center->z + $radiusXZ; $z++)
                    $positions[] = new Vector3($x, $y, $z);

        foreach($positions as $pos)
            $center->getWorld()->setBlockAt($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ(), VanillaBlocks::AIR());
    }

    public static function createFloor(World $level, int $startX, int $endX, int $startZ, int $endZ, int $y) : void {
        $positions = [];

        for($x = $startX; $x <= $endX; $x++)
            for($z = $startZ; $z <= $endZ; $z++)
                $positions[] = new Vector3($x, $y, $z);

        foreach($positions as $pos)
            $level->setBlockAt($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ(), VanillaBlocks::OBSIDIAN());
    }

    public static function createStructure(Position $heartPosition) : void{
        $level = $heartPosition->getWorld();

        $x = $heartPosition->getX();
        $y = $heartPosition->getY();
        $z = $heartPosition->getZ();

        $instance = BlockFactory::getInstance();
        
        $level->setBlock(new Vector3($x+3, $y, $z-2), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x+3, $y+3, $z-2), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x+3, $y, $z+2), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x+3, $y+3, $z+2), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x+2, $y, $z+3), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x+2, $y+3, $z+3), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x-2, $y, $z+3), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x-2, $y+3, $z+3), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x-3, $y+3, $z-2), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x-3, $y+3, $z+2), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x-3, $y, $z-2), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x-3, $y, $z+2), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x-2, $y, $z-3), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x-2, $y+3, $z-3), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x+2, $y, $z-3), VanillaBlocks::OBSIDIAN());
        $level->setBlock(new Vector3($x+2, $y+3, $z-3), VanillaBlocks::OBSIDIAN());
        
        $level->setBlock(new Vector3($x+3, $y, $z), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x-3, $y, $z), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x, $y, $z+3), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x, $y, $z-3), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x-1, $y, $z+1), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x+1, $y, $z+1), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x+1, $y, $z-1), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x-1, $y, $z-1), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x-1, $y, $z-2), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x+1, $y, $z-2), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x-1, $y, $z+2), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x+1, $y, $z+2), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x-2, $y, $z-1), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x-2, $y, $z+1), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x+2, $y, $z-1), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x+2, $y, $z+1), $instance->get(BlockLegacyIds::CARPET, 14));
        $level->setBlock(new Vector3($x+2, $y, $z+2), $instance->get(44, 5));
        $level->setBlock(new Vector3($x+2, $y, $z-2), $instance->get(44, 5));
        $level->setBlock(new Vector3($x-2, $y, $z-2), $instance->get(44, 5));
        $level->setBlock(new Vector3($x-2, $y, $z+2), $instance->get(44, 5));
        $level->setBlock(new Vector3($x+2, $y+3, $z+2), $instance->get(44, 13));
        $level->setBlock(new Vector3($x+2, $y+3, $z-2), $instance->get(44, 13));
        $level->setBlock(new Vector3($x-2, $y+3, $z-2), $instance->get(44, 13));
        $level->setBlock(new Vector3($x-2, $y+3, $z+2), $instance->get(44, 13));
        $level->setBlock(new Vector3($x+1, $y+3, $z), $instance->get(109, 5));
        $level->setBlock(new Vector3($x-1, $y+3, $z), $instance->get(109, 4));
        $level->setBlock(new Vector3($x, $y+3, $z+1), $instance->get(109, 7));
        $level->setBlock(new Vector3($x, $y+3, $z-1), $instance->get(109, 6));
        $level->setBlock(new Vector3($x-1, $y+3, $z+1), $instance->get(109, 7));
        $level->setBlock(new Vector3($x+1, $y+3, $z+1), $instance->get(109, 7));
        $level->setBlock(new Vector3($x-1, $y+3, $z-1), $instance->get(109, 6));
        $level->setBlock(new Vector3($x+1, $y+3, $z-1), $instance->get(109, 6));
        $level->setBlock(new Vector3($x-2, $y+1, $z+3), $instance->get(109, 1));
        $level->setBlock(new Vector3($x+2, $y+1, $z+3), $instance->get(109, 0));
        $level->setBlock(new Vector3($x-2, $y+1, $z-3), $instance->get(109, 1));
        $level->setBlock(new Vector3($x+2, $y+1, $z-3), $instance->get(109, 0));
        $level->setBlock(new Vector3($x+3, $y+1, $z-2), $instance->get(109, 3));
        $level->setBlock(new Vector3($x+3, $y+1, $z+2), $instance->get(109, 2));
        $level->setBlock(new Vector3($x-3, $y+1, $z-2), $instance->get(109, 3));
        $level->setBlock(new Vector3($x-3, $y+1, $z+2), $instance->get(109, 2));
        $level->setBlock(new Vector3($x+3, $y+2, $z+2), $instance->get(109, 6));
        $level->setBlock(new Vector3($x+3, $y+2, $z-2), $instance->get(109, 7));
        $level->setBlock(new Vector3($x-3, $y+2, $z+2), $instance->get(109, 6));
        $level->setBlock(new Vector3($x-3, $y+2, $z-2), $instance->get(109, 7));
        $level->setBlock(new Vector3($x+2, $y+2, $z+3), $instance->get(109, 4));
        $level->setBlock(new Vector3($x-2, $y+2, $z+3), $instance->get(109, 5));
        $level->setBlock(new Vector3($x+2, $y+2, $z-3), $instance->get(109, 4));
        $level->setBlock(new Vector3($x-2, $y+2, $z-3), $instance->get(109, 5));
        $level->setBlock(new Vector3($x+1, $y+3, $z+3), $instance->get(109, 4));
        $level->setBlock(new Vector3($x-1, $y+3, $z+3), $instance->get(109, 5));
        $level->setBlock(new Vector3($x+1, $y+3, $z-3), $instance->get(109, 4));
        $level->setBlock(new Vector3($x-1, $y+3, $z-3), $instance->get(109, 5));
        $level->setBlock(new Vector3($x+3, $y+3, $z+1), $instance->get(109, 6));
        $level->setBlock(new Vector3($x+3, $y+3, $z-1), $instance->get(109, 7));
        $level->setBlock(new Vector3($x-3, $y+3, $z+1), $instance->get(109, 6));
        $level->setBlock(new Vector3($x-3, $y+3, $z-1), $instance->get(109, 7));

        $level->setBlock(new Vector3($x, $y-1, $z), VanillaBlocks::SEA_LANTERN());
        $level->setBlock(new Vector3($x, $y+3, $z), VanillaBlocks::SEA_LANTERN());
    }
}