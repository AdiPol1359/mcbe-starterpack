<?php

namespace Gracz\data;

use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\level\Position;

class Border {

    private $x;
    private $z;

    private $radius;

    private $maxX;
    private $maxZ;

    private $minX;
    private $minZ;

    /** @var Array */
    private $safeBlocks;
    /** @var Array */
    private $unsafeBlocks;

    public function __construct($x, $z, $radius) {

        $this->x = $x;
        $this->z = $z;

        $this->maxX = $x + $radius;
        $this->minX = $x - $radius;

        $this->maxZ = $z + $radius;
        $this->minZ = $z - $radius;

        $this->radius = $radius;

        $this->safeBlocks = [
            0, 6, 8, 9, 27, 30, 31, 32, 37,
            38, 39, 40, 50, 59, 63, 64, 65,
            66, 68, 71, 78, 83, 104, 105, 106,
            141, 142, 171, 244
        ];

        $this->unsafeBlocks = [10, 11, 51, 81];

        #echo PHP_EOL.'x: '.$x.PHP_EOL;
        #echo PHP_EOL.'z: '.$z.PHP_EOL;
        #echo PHP_EOL.'maxX: '.$this->maxX.PHP_EOL;
        #echo PHP_EOL.'minX: '.$this->minX.PHP_EOL;
        #echo PHP_EOL.'maxZ: '.$this->maxZ.PHP_EOL;
        #echo PHP_EOL.'minZ: '.$this->minZ.PHP_EOL;
        #echo PHP_EOL.'radius: '.$this->radius.PHP_EOL;
    }

    public function getX() {
        return $this->x;
    }

    public function getZ() {
        return $this->z;
    }

    public function setX($x) {
        $this->x = $x;
        $this->maxX = $x + $this->radius;
        $this->minX = $x - $this->radius;
    }

    public function setZ($z) {
        $this->z = $z;
        $this->maxZ = $z + $this->radius;
        $this->minZ = $z - $this->radius;
    }

    public function setRadiusX($radius) {
        $this->radius = $radius;
        $this->maxX = $this->x + $radius;
        $this->minX = $this->x - $radius;
    }

    public function setRadiusZ($radius) {
        $this->radius = $radius;
        $this->maxZ = $this->z + $radius;
        $this->minZ = $this->z - $radius;
    }

    public function insideBorder($x, $z) {
        return !($x < $this->minX or $x > $this->maxX or $z < $this->minZ or $z > $this->maxZ);
    }

    /**
     * @param Location $location
     * @return Position
     */
    public function correctPosition($location) {

        $knockback = 3.0;

        $x = $location->getX();
        $z = $location->getZ();
        $y = $location->getY();

        #echo PHP_EOL.'MAX X: '.$this->maxX.PHP_EOL;
        #echo PHP_EOL.'MAX Z: '.$this->maxZ.PHP_EOL;

        if($x <= $this->minX) {
            $x = $this->minX + $knockback;
            #echo PHP_EOL.'DEBUG X: '.$x.PHP_EOL;
        }
        elseif($x >= $this->maxX) {
            $x = $this->maxX - $knockback;
            #echo PHP_EOL.'DEBUG X: '.$x.PHP_EOL;
        }

        if($z <= $this->minZ) {
            $z = $this->minZ + $knockback;
            #echo PHP_EOL.'DEBUG Z: '.$z.PHP_EOL;
        }
        elseif($z >= $this->maxZ) {
            $z = $this->maxZ - $knockback;
            #echo PHP_EOL.'DEBUG Z: '.$z.PHP_EOL;
        }

        $y = $this->findSafeY($location->getLevel(), $x, $y, $z);

        return new Location($x, $y, $z, $location->getYaw(), $location->getPitch());

    }

    private function findSafeY(Level $level, $x, $y, $z) {

        $top = $level->getHeightMap($x, $z) - 2;
        $bottom = 1;

        for($y1 = $y, $y2 = $y; ($y1 > $bottom) or ($y2 < $top); $y1--, $y2++) {

            #echo PHP_EOL.'Y1: '.$y1.' Y2: '.$y2;

            if($y1 > $bottom) {
                if($this->isSafe($level, $x, $y1, $z)) return $y1;
            }

            if($y2 < $top and $y2 != $y1) {
                if($this->isSafe($level, $x, $y2, $z)) return $y2;
            }

        }

        return -1;

    }

    private function isSafe(Level $level, $x, $y, $z) {

        $safe = in_array($level->getBlockIdAt($x, $y, $z), $this->safeBlocks) && in_array($level->getBlockIdAt($x, $y + 1, $z), $this->safeBlocks);

        if(!$safe) return $safe;

        $below = $level->getBlockIdAt($x, $y - 1, $z);

        #echo PHP_EOL.'LOCATION IS SAFE: '.(($safe and (!in_array($below, $this->openBlocks) or $below === 8 or $below === 9) and !in_array($below, $this->unsafeBlocks)) ? 'yes' : 'no').PHP_EOL;

        return ($safe and (!in_array($below, $this->safeBlocks) or $below === 8 or $below === 9) and !in_array($below, $this->unsafeBlocks));

    }

}
