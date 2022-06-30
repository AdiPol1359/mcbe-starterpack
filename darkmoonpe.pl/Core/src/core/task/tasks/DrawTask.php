<?php

namespace core\task\tasks;

use core\fakeinventory\FakeInventory;
use pocketmine\scheduler\Task;

class DrawTask extends Task {

    private FakeInventory $fakeInventory;

    public function __construct(FakeInventory $fakeInventory) {
        $this->fakeInventory = $fakeInventory;
    }

    public function onRun(int $currentTick) {
        $this->fakeInventory->update();
    }
}