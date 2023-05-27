<?php

declare(strict_types=1);

namespace core\users\data;

use core\Main;
use core\users\User;

class UserIgnore {

    private array $ignorePlayers = [];

    public function __construct(private User $user) {
        $this->load();
    }

    public function load() : void {
        $provider = Main::getInstance()->getProvider();

        foreach($provider->getQueryResult("SELECT * FROM 'ignore' WHERE userName = '".$this->user->getName()."'", true) as $row) {
            $this->ignorePlayers = explode(";", $row["ignoreData"]);
        }
    }
    
    public function save() : void {
        $provider = Main::getInstance()->getProvider();
        $data = "";

        foreach($this->ignorePlayers as $key => $ignorePlayer) {
            if($ignorePlayer === "") {
                continue;
            }

            $data .= $ignorePlayer . ";";
        }

        if(empty($queryResult = $provider->getQueryResult("SELECT * FROM 'ignore' WHERE userName = '".$this->user->getName()."'", true))) {
            $provider->executeQuery("INSERT INTO 'ignore' (userName, ignoreData) VALUES ('".$this->user->getName()."', '".$data."')");
        } else {
            $provider->executeQuery("UPDATE 'ignore' SET ignoreData = '".$data."' WHERE userName = '".$this->user->getName()."'");
        }
    }

    public function isIgnoring(string $nick) : bool {
        foreach($this->ignorePlayers as $key => $playerName) {
            if($playerName === $nick) {
                return true;
            }
        }

        return false;
    }

    public function ignore(string $nick) : void {
        $this->ignorePlayers[] = $nick;
    }

    public function unIgnore(string $nick) : void {
        foreach($this->ignorePlayers as $key => $ignorePlayer) {
            if($ignorePlayer === $nick) {
                unset($this->ignorePlayers[$key]);
                return;
            }
        }
    }

    public function getIgnoredPlayers() : array {
        return $this->ignorePlayers;
    }
}