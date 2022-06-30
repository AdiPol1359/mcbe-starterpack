<?php

namespace core\manager\managers\service;

use core\Main;
use core\manager\BaseManager;

class ServicesManager extends BaseManager {

    public static array $services = [];

    public static function init() : void {
        Main::getDb()->exec("CREATE TABLE IF NOT EXISTS service (id DOUBLE, nick TEXT, service INT, collected INT, time INT)");
    }

    public static function loadServices() : void{
        $data = Main::$services;

        foreach($data as $id => $values)
            self::$services[$id] = new Service($id, $values["name"], $values["cost"], $values["command"]);
    }

    public static function getService(int $id) : Service{
        return self::$services[$id];
    }

    public static function existsService(int $id) : bool{
        return !empty(Main::getDb()->query("SELECT * FROM service WHERE id = '$id'")->fetchArray());
    }

    public static function getServices(string $nick) : array{

        if(!self::hasService($nick))
            return [];

        $db = Main::getDb()->query("SELECT * FROM service WHERE nick = '$nick'");

        $services = [];

        while($row = $db->fetchArray(SQLITE3_ASSOC))
            $services[$row["id"]] = ["nick" => $row["nick"], "service" => $row["service"], "collected" => $row["collected"], "time" => $row["time"]];

        return $services;
    }

    public static function hasService(string $nick) : bool{
        return !empty(Main::getDb()->query("SELECT * FROM service WHERE nick = '$nick'")->fetchArray());
    }
}