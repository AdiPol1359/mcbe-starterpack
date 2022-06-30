<?php

namespace core\manager\managers\pet;

use pocketmine\Player;

class Pet {

    private string $name;
    private int $networkID;
    private float $width;
    private float $height;
    private float $speed;
    private int $price;
    private string $displayName;
    private bool $canFly;
    private ?PetEntity $entity;
    private ?Player $owner = null;

    public function __construct(string $name, int $networkID, float $width, float $height, float $speed, int $price, string $displayName, bool $canFly) {
        $this->name = $name;
        $this->networkID = $networkID;
        $this->width = $width;
        $this->height = $height;
        $this->speed = $speed;
        $this->price = $price;
        $this->displayName = $displayName;
        $this->canFly = $canFly;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getNetworkID() : int {
        return $this->networkID;
    }

    public function getWidth() : float {
        return $this->width;
    }

    public function getHeight() : float {
        return $this->height;
    }

    public function getSpeed() : float {
        return $this->speed;
    }

    public function getPrice() : int {
        return $this->price;
    }

    public function getDisplayName() : string {
        return $this->displayName;
    }

    public function getEntity() : ?PetEntity {
        return $this->entity;
    }

    public function setEntity(PetEntity $entity) : void {
        $this->entity = $entity;
    }

    public function getOwner() : ?Player {
        return $this->owner;
    }

    public function setOwner(Player $owner) : void {
        $this->owner = $owner;
    }

    public function canFly() : bool{
        return $this->canFly;
    }
}