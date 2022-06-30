<?php

namespace core\manager\managers\particle\particles\custom;

use pocketmine\level\particle\GenericParticle;
use pocketmine\level\particle\Particle;
use pocketmine\math\Vector3;

class Rain extends GenericParticle{

    public function __construct(Vector3 $pos, int $scale = 0){
        parent::__construct($pos, Particle::TYPE_RAIN_SPLASH, $scale);
    }
}