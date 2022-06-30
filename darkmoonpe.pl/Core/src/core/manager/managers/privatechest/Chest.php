<?php

namespace core\manager\managers\privatechest;

use pocketmine\math\Vector3;

class Chest{

    private string $owner;
    private string $level;
    private Vector3 $vector;

    public function __construct(string $owner, Vector3 $position, string $level) {
        $this->owner = $owner;
        $this->vector = $position;
        $this->level = $level;
    }

    public function getOwner() : string{
        return $this->owner;
    }

    public function getChestPosition() : Vector3{
        return $this->vector;
    }

    public function getLevel() : string {
        return $this->level;
    }
}