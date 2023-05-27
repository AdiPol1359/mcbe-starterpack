<?php

namespace core\managers\turbodrop;

use core\Main;
use JetBrains\PhpStorm\Pure;

class TurboDropManager {

    /** @var TurboDrop[] */
    private array $turbodrops = [];

    public function __construct(private Main $plugin) {
        $this->load();
    }

    public function load() : void {
        $provider = $this->plugin->getProvider();

        foreach($provider->getQueryResult("SELECT * FROM turbodrop", true) as $row) {
            if($row["expire"] <= time()) {
                continue;
            }

            $this->turbodrops[] = new TurboDrop($row["id"], $row["founder"], (bool)$row["server"], $row["expire"], true);

        }
    }

    public function save() : void {
        $provider = $this->plugin->getProvider();

        foreach($this->turbodrops as $key => $turboDrop) {
            if($turboDrop->getExpireTime() <= time() && $turboDrop->isFromDataBase()) {
                $provider->executeQuery("DELETE FROM turbodrop WHERE id = '" . $turboDrop->getId() . "'");
            } else if(!$turboDrop->isFromDataBase()) {
                $provider->executeQuery("INSERT INTO turbodrop (id, founder, server, expire) VALUES ('" . $turboDrop->getId() . "', '" . $turboDrop->getFounder() . "', '" . $turboDrop->isServer() . "', '" . $turboDrop->getExpireTime() . "')");
            }
        }
    }

    public function addTurboDrop(string $founder, bool $server, int $expire) : void {
        $this->turbodrops[] = new TurboDrop($this->getHighestId() + 1, $founder, $server, $expire);
    }

    #[Pure] public function getHighestId() : int {
        $id = 0;

        foreach($this->turbodrops as $key => $turboDrop) {
            if($turboDrop->getId() > $id)
                $id = $turboDrop->getId();
        }

        return $id;
    }

    public function isTurboDropEnabledFor(string $player) : bool {
        foreach($this->turbodrops as $key => $turboDrop) {
            if($turboDrop->getExpireTime() <= time()) {
                if(!$turboDrop->isFromDataBase())
                    unset($this->turbodrops[$key]);

                continue;
            }

            if($turboDrop->isServer() || $turboDrop->getFounder() === $player)
                return true;
        }

        return false;
    }

    public function getTurboDropFor(string $player) : ?TurboDrop {
        foreach($this->turbodrops as $key => $turboDrop) {
            if($turboDrop->getExpireTime() <= time()) {
                if(!$turboDrop->isFromDataBase())
                    unset($this->turbodrops[$key]);

                continue;
            }

            if($turboDrop->isServer() || $turboDrop->getFounder() === $player)
                return $turboDrop;
        }

        return null;
    }
}