<?php

namespace core\tile\tiles;

use pocketmine\tile\Hopper as PMHopperTile;

class Hopper extends PMHopperTile {

    private int $tick = 15;

    public function onUpdate() : bool{

        if($this->closed){
            return false;
        }

        $this->tick++;

        if($this->tick < 15)
            return true;

        $transfer = $this->pushItems();

        if(!$transfer and !$this->isFull())
            $transfer = $this->pullItems();

        if($transfer)
            $this->setTransferCooldown($this->transferCooldown);

        $this->tick = 0;

        return true;
    }
}