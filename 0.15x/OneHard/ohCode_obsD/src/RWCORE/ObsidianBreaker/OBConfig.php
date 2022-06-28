<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 19/11/2016
 * Time: 13:08
 */

namespace RWCORE\ObsidianBreaker;

use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\level\Level;


class OBConfig{

    public static function parseBlockList(array $array = [])
    {
        $blocks = [];
        foreach ($array as $data) {
            $temp = explode(",", str_replace(" ", "", $data));
            $blocks[$temp[0]] = $temp[1];
        }
        return $blocks;
    }

    public static function getBlockString(Block $block)
    {
        return $block->__toString() . "x:{$block->x},y:{$block->y},z:{$block->z}";
    }

    public static function getExplosionAffectedBlocks(Position $center, $size)
    {
        if ($size < 0.1) {
            return false;
        }
        $affectedBlocks = [];
        $rays = 16;
        $stepLen = 0.3;
        $vector = new Vector3(0, 0, 0);
        $vBlock = new Vector3(0, 0, 0);
        $mRays = intval($rays - 1);
        for ($i = 0; $i < $rays; ++$i) {
            for ($j = 0; $j < $rays; ++$j) {
                for ($k = 0; $k < $rays; ++$k) {
                    if ($i === 0 or $i === $mRays or $j === 0 or $j === $mRays or $k === 0 or $k === $mRays) {
                        $vector->setComponents($i / $mRays * 2 - 1, $j / $mRays * 2 - 1, $k / $mRays * 2 - 1);
                        $vector->setComponents(($vector->x / ($len = $vector->length())) * $stepLen, ($vector->y / $len) * $stepLen, ($vector->z / $len) * $stepLen);
                        $pointerX = $center->x;
                        $pointerY = $center->y;
                        $pointerZ = $center->z;
                        for ($blastForce = $size * (mt_rand(700, 1300) / 1000); $blastForce > 0; $blastForce -= $stepLen * 0.75) {
                            $x = (int)$pointerX;
                            $y = (int)$pointerY;
                            $z = (int)$pointerZ;
                            $vBlock->x = $pointerX >= $x ? $x : $x - 1;
                            $vBlock->y = $pointerY >= $y ? $y : $y - 1;
                            $vBlock->z = $pointerZ >= $z ? $z : $z - 1;
                            if ($vBlock->y < 0 or $vBlock->y > 127) {
                                break;
                            }
                            $block = $center->level->getBlock($vBlock);
                            if ($block->getId() !== 0) {
                                if ($blastForce > 0) {
                                    $blastForce -= ($block->getResistance() / 5 + 0.3) * $stepLen;
                                    if (!isset($affectedBlocks[$index = Level::blockHash($block->x, $block->y, $block->z)])) {
                                        $affectedBlocks[$index] = $block;
                                    }
                                }
                            }
                            $pointerX += $vector->x;
                            $pointerY += $vector->y;
                            $pointerZ += $vector->z;
                        }
                    }
                }
            }
        }
        return $affectedBlocks;
    }

}