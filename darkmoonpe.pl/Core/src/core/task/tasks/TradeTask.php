<?php

namespace core\task\tasks;

use core\fakeinventory\inventory\TradeInventory;
use pocketmine\scheduler\Task;

class TradeTask extends Task{

    private TradeInventory $trade;

    public function __construct(TradeInventory $trade) {
        $this->trade = $trade;
    }

    public function onRun(int $currentTick) {
        $this->trade->countDown();
    }
}