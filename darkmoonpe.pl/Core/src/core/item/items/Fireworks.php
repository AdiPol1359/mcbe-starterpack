<?php

namespace core\item\items;

use pocketmine\item\Fireworks as PMFireWorks;

class Fireworks extends PMFireWorks {
    public function getRandomizedFlightDuration() : int {
        return 0;
    }
}