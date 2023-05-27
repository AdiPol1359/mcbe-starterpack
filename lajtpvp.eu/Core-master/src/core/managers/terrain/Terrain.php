<?php

declare(strict_types=1);

namespace core\managers\terrain;

use pocketmine\world\Position;

class Terrain {

    private ?string $name;
    private Position $pos1;
    private Position $pos2;

    private int $priority;

    private array $settings;

    public function __construct(?string $name, int $priority, Position $pos1, Position $pos2, array $settings = []) {
        $this->name = $name;
        $this->pos1 = $pos1;
        $this->pos2 = $pos2;

        $this->priority = $priority;
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
            && $position->getWorld()->getDisplayName() === $pos1->getWorld()->getDisplayName()
            && $position->getWorld()->getDisplayName() === $pos1->getWorld()->getDisplayName();
    }

    public function isSettingEnabled(string $name) : bool {
        return $this->settings[$name] ?? false;
    }

    public function getSettings() : array {
        return $this->settings;
    }

    public function switchSetting(string $name, bool|null $value = null) : void {
        $this->isSettingEnabled($name) ? $bool = false : $bool = true;
        $value !== null ? $this->settings[$name] = $value : $this->settings[$name] = $bool;
    }

    public function getPriority() : int {
        return $this->priority;
    }

    public function setPriority(int $priority) : void {
        $this->priority = $priority;
    }
}