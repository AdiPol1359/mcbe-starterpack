<?php

declare(strict_types=1);

namespace core\users\data;

use core\Main;
use core\users\User;

class UserBank {

    private array $data = [];

    public function __construct(private User $user) {
        $this->load();
    }

    public function load() : void {
        $provider = Main::getInstance()->getProvider();

        if(empty($provider->getQueryResult("SELECT * FROM 'bank' WHERE nick = '".$this->user->getName()."'", true))) {
            $provider->executeQuery("INSERT INTO 'bank' (nick, gold) VALUES ('".$this->user->getName()."', 0)");
        }

        $bank = [];

        foreach($provider->getQueryResult("SELECT * FROM 'bank' WHERE nick = '".$this->user->getName()."'", true) as $key => $row) {
            foreach($row as $r => $rowValue) {
                if($r === "nick")
                    continue;

                $bank[$r] = $rowValue;
            }
        }

        $this->data = $bank;
    }

    public function save() : void {
        foreach($this->data as $data => $status) {
            Main::getInstance()->getProvider()->executeQuery("UPDATE 'bank' SET '".$data."' = '".$status."' WHERE nick = '{$this->user->getName()}'");
        }
    }

    public function getBankStatus(string $data) : int {
        return (int)$this->data[$data];
    }

    public function setBankStatus(string $data, int $status = 1) : void{
        $this->data[$data] = $status;
    }

    public function addBankStatus(string $data, int $status = 1) : void{
        $this->data[$data] += $status;
    }

    public function reduceBankStatus(string $data, int $status = 1) : void{
        $this->data[$data] -= $status;
    }
}