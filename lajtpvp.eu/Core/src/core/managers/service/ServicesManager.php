<?php

declare(strict_types=1);

namespace core\managers\service;

use core\Main;
use core\utils\Settings;
use JetBrains\PhpStorm\Pure;

class ServicesManager {

    /** @var Service[] */
    public array $services = [];

    public function __construct(private Main $plugin) {
        $this->loadServices();
    }
    
    public function loadServices() : void{
        $data = Settings::$SERVICES;

        foreach($data as $id => $values)
            $this->services[] = new Service($id, $values["name"], $values["command"], $values["commandName"]);
    }

    #[Pure] public function getService(int $id) : ?Service{
        foreach($this->services as $service) {
            if($service->getId() === $id)
                return $service;
        }

        return null;
    }

    public function existsService(int $id) : bool{
        return !empty($this->plugin->getProvider()->getQueryResult("SELECT * FROM service WHERE id = '$id'", true));
    }

    public function getServices(string $nick) : array{
        $services = [];

        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM service WHERE nick = '".$nick."'", true) as $row) {
            $services[] = ["id" => $row["id"], "nick" => $row["nick"], "service" => $row["service"], "collected" => (bool)$row["collected"], "time" => $row["time"]];
        }

        return $services;
    }

    public function hasService(string $nick) : bool{
        return !empty($this->plugin->getProvider()->getQueryResult("SELECT * FROM service WHERE nick = '$nick'", true));
    }

    #[Pure] public function getCommandNames() : array {
        $names = [];

        foreach($this->services as $service)
            $names[] = $service->getCommandName();

        return $names;
    }

    #[Pure] public function getServiceByCommandName(string $name) : ?Service {
        foreach($this->services as $service) {
            if($name === $service->getCommandName())
                return $service;
        }

        return null;
    }
}