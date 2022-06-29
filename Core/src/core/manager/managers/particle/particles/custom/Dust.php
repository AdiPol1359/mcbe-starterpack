<?php

namespace core\manager\managers\particle\particles\custom;

use pocketmine\level\particle\GenericParticle;
use pocketmine\math\Vector3;

class Dust extends GenericParticle{

    public function __construct(Vector3 $pos, $r, $g, $b, $a = 255){
        parent::__construct($pos, 31, (($a & 0xff) << 24) | (($r & 0xff) << 16) | (($g & 0xff) << 8) | ($b & 0xff));
    }
}