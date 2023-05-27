<?php

declare(strict_types=1);

namespace core\users\data;

use core\Main;
use core\managers\drop\Drop;
use core\users\User;
use core\utils\Settings;
use JetBrains\PhpStorm\Pure;

class UserBackpack {

    private int $backpackSize;
    private array $data = [];

    public function __construct(private User $user) {
        $this->load();
    }

    public function load() : void {
        $provider = Main::getInstance()->getProvider();

        $this->backpackSize = Settings::$DEFAULT_BACKPACK_SIZE;
        $data = [];

        foreach(Main::getInstance()->getDropManager()->getDrop() as $drop) {
            if($drop->isDefault()) {
                continue;
            }

            $data[$drop->getDropId()] = 0;
        }

        if(!empty($queryResult = $provider->getQueryResult("SELECT * FROM 'backpack' WHERE userName = '".$this->user->getName()."'", true))) {
            foreach($queryResult as $result) {
                $this->backpackSize = (int) $result["backpackSize"];

                $backpackData = explode(";", $result["backpackData"]);
                foreach($backpackData as $backpackDatum) {
                    if($backpackDatum === "") {
                        continue;
                    }

                    $backpack = explode("=", $backpackDatum);

                    if($backpack[0] === "" || $backpack[1] === "") {
                        continue;
                    }

                    if(isset($data[0])) {
                        $data[0] = (int) $backpack[1];
                    }
                }
            }
        }

        $this->data = $data;
    }

    public function save() : void {
        $data = "";
        $provider = Main::getInstance()->getProvider();

        foreach($this->data as $backpackName => $backpackStatus) {
            $data .= $backpackName . "=" . $backpackStatus . ";";
        }

        if(empty($queryResult = $provider->getQueryResult("SELECT * FROM 'backpack' WHERE userName = '".$this->user->getName()."'", true))) {
            $provider->executeQuery("INSERT INTO 'backpack' (userName, backpackSize, backpackData) VALUES ('".$this->user->getName()."', '".$this->backpackSize."', '".$data."')");
        } else {
            $provider->executeQuery("UPDATE 'backpack' SET backpackData = '".$data."', backpackSize = '".$this->backpackSize."' WHERE userName = '".$this->user->getName()."'");
        }
    }

    #[Pure] public function getItemCount(Drop $drop) : int {
        return (int) $this->data[$drop->getDropId()];
    }

    #[Pure] public function getMaxBackpackSize() : int {
        return $this->backpackSize;
    }

    #[Pure] public function getItemsCountInBackpack() : int {
        $count = 0;

        foreach($this->data as $dropId => $itemAmount) {
            $count += $itemAmount;
        }

        return $count;
    }

    public function getData() : array {
        return $this->data;
    }

    public function setBackpackSize(int $size) : void {
        $this->backpackSize = $size;
    }

    public function addItem(Drop $backpack, int $value = 1) : void {
        if(!isset($this->data[$backpack->getDropId()])) {
            return;
        }

        $this->data[$backpack->getDropId()] += $value;
    }

    public function reduceItem(Drop $backpack, int $value = 1) : void {
        if(!isset($this->data[$backpack->getDropId()])) {
            return;
        }

        $this->data[$backpack->getDropId()] -= $value;
    }

    public function setItem(Drop $backpack, int $value = 1) : void {
        if(!isset($this->data[$backpack->getDropId()])) {
            return;
        }

        $this->data[$backpack->getDropId()] = $value;
    }

    public function setAllDrops(int $value = 1) : void {
        foreach($this->data as $key => $backpack) {
            $this->data[$key] = $value;
        }
    }
}