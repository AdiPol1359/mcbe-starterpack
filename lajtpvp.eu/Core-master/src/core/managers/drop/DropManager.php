<?php

declare(strict_types=1);

namespace core\managers\drop;

use core\utils\Settings;
use JetBrains\PhpStorm\Pure;

class DropManager {

    /** @var Drop[] */
    private array $drop = [];

    public function loadDrop() : void {
        foreach(Settings::$DROP as $key => $data) {
            $this->drop[$key] = new Drop($key, $data["dropName"], $data["name"], $data["color"], $data["chance"], $data["default"], $data["exp"], $data["slot"], $data["fortune"], $data["turbo"], $data["message"], $data["deposit"], $data["bonuses"], $data["tool"], $data["amount"], $data["drop"]);
        }
    }

    public function getDrop() : array {
        return $this->drop;
    }

    #[Pure] public function getDropByName(string $dropName) : ?Drop {
        foreach($this->drop as $key => $drop) {
            if($drop->getDropName() === $dropName) {
                return $drop;
            }
        }

        return null;
    }

    public function getDropById(int $dropId) : ?Drop {
        return $this->drop[$dropId] ?? null;
    }

    public function addDrop(int $key, Drop $drop) : void {
        $this->drop[$key] = $drop;
    }

    public function removeDrop(int $key) : void {
        unset($this->drop[$key]);
    }
}