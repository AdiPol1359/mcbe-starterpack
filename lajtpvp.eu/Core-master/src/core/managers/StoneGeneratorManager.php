<?php

declare(strict_types=1);

namespace core\managers;

use pocketmine\block\BlockLegacyIds;
use pocketmine\world\Position;

class StoneGeneratorManager {

    public static function isStoneGenerator(Position $position) : bool{
        for($i = -2, $check = ["x" => false, "y" => false, "z" => false], $vector = clone $position; $i <= 2; $i++){
            foreach($check as $coordinate => $value) {
                if($value)
                    continue;

                $vector->$coordinate += $i;

                if($position->getWorld()->getBlock($vector)->getId() === BlockLegacyIds::END_STONE)
                    return true;

                elseif($i > 1){
                    $check[$coordinate] = true;
                    $i = -2;
                }

                $vector = clone $position;
            }
        }

        return false;
    }
}