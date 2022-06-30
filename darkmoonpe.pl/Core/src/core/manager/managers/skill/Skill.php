<?php

namespace core\manager\managers\skill;

class Skill {

    private ?int $id;
    private ?string $name;
    private ?string $description;
    private ?float $cost;

    public function __construct(int $id, string $name, string $description, float $cost) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->cost = $cost;
    }

    public function getId() : ?int {
        return $this->id;
    }

    public function getName() : ?string {
        return $this->name;
    }

    public function getDescription() : ?string {
        return $this->description;
    }

    public function getCost() : ?float {
        return $this->cost;
    }
}