<?php

namespace core\managers\service;

class Service{

    private int $id;
    private string $name;
    private string $command;
    private string $commandName;

    public function __construct(int $id, string $name, string $command, string $commandName){
        $this->id = $id;
        $this->name = $name;
        $this->command = $command;
        $this->commandName = $commandName;
    }

    public function getCommand() : string{
        return $this->command;
    }

    public function getCommandName() : string {
        return $this->commandName;
    }

    public function getId() : int{
        return $this->id;
    }

    public function getName() : string {
        return $this->name;
    }
}