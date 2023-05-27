<?php

declare(strict_types=1);

namespace core\managers\warp;

use pocketmine\world\Position;

class Warp {

    public function __construct(private string $name, private Position $position) {
    }

    public function getName() : string {
        return $this->name;
    }

    public function getPosition() : Position {
        return $this->position;
    }
}