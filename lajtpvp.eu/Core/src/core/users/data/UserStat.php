<?php

declare(strict_types=1);

namespace core\users\data;

use core\Main;
use core\users\User;
use core\utils\Settings;
use JetBrains\PhpStorm\Pure;

class UserStat {

    private array $data = [];

    public function __construct(private User $user) {
        $this->load();
    }

    public function load() : void {
        $provider = Main::getInstance()->getProvider();

        $data = [];

        foreach(Settings::$STATS as $statName => $defaultValue) {
            $data[$statName] = $defaultValue;
        }

        if(!empty($queryResult = $provider->getQueryResult("SELECT * FROM 'stats' WHERE userName = '".$this->user->getName()."'", true))) {
            foreach($queryResult as $result) {
                $statData = explode(";", $result["statData"]);
                foreach($statData as $statDatum) {
                    if($statDatum === "") {
                        continue;
                    }

                    $stat = explode("=", $statDatum);

                    if($stat[0] === "" || $stat[1] === "") {
                        continue;
                    }

                    if(isset($data[0])) {
                        $data[0] = (int) $stat[1];
                    }
                }
            }
        }

        $this->data = $data;
    }

    public function save() : void {
        $data = "";
        $provider = Main::getInstance()->getProvider();

        foreach($this->data as $statName => $statValue) {
            $data .= $statName . "=" . $statValue . ";";
        }

        if(empty($queryResult = $provider->getQueryResult("SELECT * FROM 'stats' WHERE userName = '".$this->user->getName()."'", true))) {
            $provider->executeQuery("INSERT INTO 'stats' (userName, statData) VALUES ('".$this->user->getName()."', '".$data."')");
        } else {
            $provider->executeQuery("UPDATE 'stats' SET statData = '".$data."' WHERE userName = '".$this->user->getName()."'");
        }
    }

    #[Pure] public function getStat(string $statName) : int {
        return (int) $this->data[$statName];
    }

    public function getData() : array {
        return $this->data;
    }

    public function setStat(string $statName, int $value = 0) : void {
        $this->data[$statName] = $value;
    }

    public function addStat(string $statName, int $value = 1) : void {
        $this->data[$statName] += $value;
    }

    public function reduceStat(string $statName, int $value = 1) : void {
        $this->data[$statName] -= $value;
    }

    public function existsStat(string $statName) : bool {
        return isset($this->data[$statName]);
    }
}