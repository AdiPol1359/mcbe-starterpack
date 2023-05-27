<?php

declare(strict_types=1);

namespace core\users\data;

use core\users\User;
use pocketmine\world\Position;

class UserTerrain {

    private ?Position $pos1;
    private ?Position $pos2;

    public function __construct(private User $user) {}

    public function getPos1() : ?Position {
        return $this->pos1;
    }

    public function setPos1($pos1) : void {
        $this->pos1 = $pos1;
    }

    public function getPos2() : ?Position {
        return $this->pos2;
    }

    public function setPos2($pos2) : void {
        $this->pos2 = $pos2;
    }
}