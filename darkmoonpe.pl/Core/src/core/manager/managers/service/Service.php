<?php

namespace core\manager\managers\service;

class Service{

    private int $id;
    private string $name;
    private float $cost;
    private string $command;

    public function __construct(int $id, string $name, float $cost, string $command){
        $this->id = $id;
        $this->name = $name;
        $this->cost = $cost;
        $this->command = $command;
    }

    public function getCommand() : string{
        return $this->command;
    }

    public function getId() : int{
        return $this->id;
    }

    public function getName() : string{
        return $this->name;
    }

    public function getCost() :  float{
        return $this->cost;
    }
}