<?php

declare(strict_types=1);

namespace core\managers\home;

use pocketmine\world\Position;

class Home {

    public function __construct(
        private string $nick,
        private string $name,
        private Position $position
    ) {}

    public function getNick() : string {
        return $this->nick;
    }

    public function getHomeName() : string {
        return $this->name;
    }

    public function getPosition() : Position {
        return $this->position;
    }
}