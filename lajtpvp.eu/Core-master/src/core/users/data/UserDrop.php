<?php

declare(strict_types=1);

namespace core\users\data;

use core\Main;
use core\managers\drop\Drop;
use core\users\User;
use core\utils\Settings;
use JetBrains\PhpStorm\Pure;

class UserDrop {

    private array $data = [];

    public function __construct(private User $user) {
        $this->load();
    }

    public function load() : void {
        $provider = Main::getInstance()->getProvider();

        $data = [];

        foreach(Settings::$DROP as $key => $dropData) {
            $data[$key] = true;
        }

        if(!empty($queryResult = $provider->getQueryResult("SELECT * FROM 'drop' WHERE userName = '".$this->user->getName()."'", true))) {
            foreach($queryResult as $result) {
                $dropData = explode(";", $result["dropData"]);
                foreach($dropData as $dropDatum) {
                    if($dropDatum === "") {
                        continue;
                    }

                    $drop = explode("=", $dropDatum);

                    if($drop[0] === "" || $drop[1] === "") {
                        continue;
                    }

                    if(isset($data[0])) {
                        $data[0] = (bool) $drop[1];
                    }
                }
            }
        }

        $this->data = $data;
    }

    public function save() : void {
        $data = "";
        $provider = Main::getInstance()->getProvider();

        foreach($this->data as $dropName => $dropStatus) {
            $data .= $dropName . "=" . $dropStatus . ";";
        }

        if(empty($queryResult = $provider->getQueryResult("SELECT * FROM 'drop' WHERE userName = '".$this->user->getName()."'", true))) {
            $provider->executeQuery("INSERT INTO 'drop' (userName, dropData) VALUES ('".$this->user->getName()."', '".$data."')");
        } else {
            $provider->executeQuery("UPDATE 'drop' SET dropData = '".$data."' WHERE userName = '".$this->user->getName()."'");
        }
    }

    #[Pure] public function isDropEnabled(Drop $drop) : bool {
        return $this->data[$drop->getDropId()] ?? true;
    }

    public function getData() : array {
        return $this->data;
    }

    public function switchDrop(Drop $drop, bool|null $value = null) : void {
        if(!isset($this->data[$drop->getDropId()])) {
            return;
        }

        $this->data[$drop->getDropId()] = ($value === null ? !$this->data[$drop->getDropId()] : $value);
    }

    public function setAllDrops(bool $value = true) : void {
        foreach($this->data as $key => $drop) {
            $this->data[$key] = $value;
        }
    }
}