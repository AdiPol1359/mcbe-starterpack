<?php

namespace core\manager\managers\terrain;

use pocketmine\level\Position;

class Terrain {

    private ?string $name;
    private Position $pos1;
    private Position $pos2;

    private int $priority;

    private array $settings;

    public function __construct(?string $name, int $priotity, Position $pos1, Position $pos2, array $settings = []) {
        $this->name = $name;
        $this->pos1 = $pos1;
        $this->pos2 = $pos2;

        if(empty($settings)){
            $settings = [
                "break_block" => ["name" => "Niszczenie blokow", "status" => true],
                "place_block" => ["name" => "Stawianie blokow", "status" => true],
                "interact" => ["name" => "Interakcja", "status" => true],
                "fighting" => ["name" => "Bicie sie", "status" => true],
                "use_command" => ["name" => "Uzywanie komend", "status" => true],
                "damage" => ["name" => "Obrazenia", "status" => true],
                "lose_food" => ["name" => "Utrata glodu", "status" => true]
            ];
        }

        $this->priority = $priotity;
        $this->settings = $settings;
    }

    public function getName() : ?string {
        return $this->name;
    }

    public function getPos1() : Position {
        return $this->pos1;
    }

    public function getPos2() : Position {
        return $this->pos2;
    }

    public function contains(Position $position) : bool {
        $pos = $position->floor();
        $pos1 = $this->pos1;
        $pos2 = $this->pos2;

        return
               $pos->getFloorX() <= max($pos1->getFloorX(), $pos2->getFloorX())
            && $pos->getFloorX() >= min($pos1->getFloorX(), $pos2->getFloorX())
            && $pos->getFloorY() <= max($pos1->getFloorY(), $pos2->getFloorY())
            && $pos->getFloorY() >= min($pos1->getFloorY(), $pos2->getFloorY())
            && $pos->getFloorZ() <= max($pos1->getFloorZ(), $pos2->getFloorZ())
            && $pos->getFloorZ() >= min($pos1->getFloorZ(), $pos2->getFloorZ())
            && $position->getLevelNonNull()->getName() === $pos1->getLevelNonNull()->getName()
            && $position->getLevelNonNull()->getName() === $pos1->getLevelNonNull()->getName();
    }

    public function isSettingEnabled(string $name) : bool {
        return isset($this->settings[$name]) ? $this->settings[$name]["status"] : false;
    }

    public function getSettings() : array {
        return $this->settings;
    }

    public function switchSetting(string $name, ?bool $value = null) : void {

        $this->isSettingEnabled($name) ? $bool = false : $bool = true;
        $value !== null ? $this->settings[$name]["status"] = $value : $this->settings[$name]["status"] = $bool;
    }

    public function getPritority() : int {
        return $this->priority;
    }

    public function setPriority(int $priority) : void {
        $this->priority = $priority;
    }
}