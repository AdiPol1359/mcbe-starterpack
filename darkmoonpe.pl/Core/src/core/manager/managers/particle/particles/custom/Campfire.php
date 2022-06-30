<?php

namespace core\manager\managers\particle\particles\custom;

use pocketmine\level\particle\GenericParticle;
use pocketmine\math\Vector3;

class Campfire extends GenericParticle{

    public function __construct(Vector3 $pos, int $scale = 0){
        parent::__construct($pos, 66, $scale);
    }
}