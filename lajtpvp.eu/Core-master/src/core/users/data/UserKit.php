<?php

declare(strict_types=1);

namespace core\users\data;

use core\Main;
use core\users\User;
use core\utils\Settings;

class UserKit {

    private array $data = [];

    public function __construct(private User $user) {
        $this->load();
    }

    public function load() : void {
        //TODO: zrobić obiekt z informacjami o kitach
        $provider = Main::getInstance()->getProvider();

        if(empty($provider->getQueryResult("SELECT * FROM 'kit' WHERE nick = '".$this->user->getName()."'"))) {
            $data = [];

            foreach(Settings::$KITS as $kit => $kitData) {
                $data[$kit] = 0;
            }

            $provider->executeQuery("INSERT INTO 'kit' (nick, kits) VALUES ('" . $this->user->getName() . "', '" . json_encode($data) . "')");
        }

        foreach($provider->getQueryResult("SELECT * FROM 'kit' WHERE nick = '".$this->user->getName()."'", true) as $row) {
            $kitData = json_decode($row["kits"], true);

            foreach($kitData as $kitName => $kitDelay)
                $this->data[$kitName] = $kitDelay;
        }
    }

    public function save() : void {
        Main::getInstance()->getProvider()->executeQuery("UPDATE kit SET kits = '".json_encode($this->data)."' WHERE nick = '".$this->user->getName()."'");
    }

    public function canUseKit(string $kitName) : bool {
        return $this->data[$kitName] <= time();
    }

    public function getUseKitTime(string $kitName) : int {
        return $this->data[$kitName];
    }

    public function setKit(string $kitName, int $time = 1) : void{
        $this->data[$kitName] = $time;
    }
}