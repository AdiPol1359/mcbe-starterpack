<?php

declare(strict_types=1);

namespace core\users\data;

use core\Main;
use core\managers\home\Home;
use core\users\User;
use core\utils\VectorUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\world\Position;

class UserHome {

    /** @var Home[] */
    private array $data = [];

    public function __construct(private User $user) {
        $this->load();
    }

    public function load() : void {
        $provider = Main::getInstance()->getProvider();
        $data = [];

        foreach($provider->getQueryResult("SELECT * FROM home WHERE nick = '".$this->user->getName()."'", true) as $row) {
            $data[] = new Home($this->user->getName(), $row["name"], VectorUtil::getPositionFromData($row["position"]));
        }

        $this->data = $data;
    }

    public function save() : void {
        $provider = Main::getInstance()->getProvider();

        foreach($provider->getQueryResult("SELECT * FROM home WHERE nick = '".$this->user->getName()."'", true) as $row) {
            if(!$this->getHome($row["name"])) {
                $provider->executeQuery("DELETE FROM home WHERE name = '" . $row["name"] . "'");
            }
        }

        foreach($this->data as $datum => $home) {
            foreach($provider->getQueryResult("SELECT * FROM home WHERE name = '" . $home->getHomeName() . "' AND nick = '".$this->user->getName()."'", true) as $row) {
                if($row["position"] !== $home->getPosition()->__toString()) {
                    $provider->executeQuery("DELETE FROM home WHERE name = '" . $home->getHomeName() . "' AND nick = '".$this->user->getName()."'");
                }
            }

            if(empty($provider->getQueryResult("SELECT * FROM home WHERE name = '" . $home->getHomeName() . "'", true))) {
                $provider->executeQuery("INSERT INTO home (nick, name, position) VALUES ('".$this->user->getName()."', '" . $home->getHomeName() . "', '" . $home->getPosition()->__toString() . "')");
            }
        }
    }

    public function createHome(string $name, Position $position) : void {
        if($this->getHome($name)) {
            return;
        }

        $this->data[] = new Home($this->user->getName(), $name, $position);
    }

    public function deleteHome(string $name) : void {
        foreach($this->data as $datum => $home) {
            if($home->getHomeName() === $name) {
                unset($this->data[$datum]);
            }
        }
    }

    #[Pure] public function getHome(string $name) : ?Home {
        foreach($this->data as $datum => $home) {
            if($home->getHomeName() === $name) {
                return $home;
            }
        }
        
        return null;
    }

    #[Pure] public function getHomeNames() : array {
        $names = [];

        foreach($this->data as $datum => $home) {
            $names[] = $home->getHomeName();
        }

        return $names;
    }

    public function getHomes() : array {
        return $this->data;
    }
}