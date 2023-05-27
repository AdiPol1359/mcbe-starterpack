<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\market;

use core\inventories\FakeInventory;
use core\inventories\FakeInventorySize;
use core\Main;
use pocketmine\player\Player;

abstract class MarketInventory extends FakeInventory {

    public function __construct(string $title = "FakeInventory", int $size = FakeInventorySize::LARGE_CHEST) {
        parent::__construct($title, $size);
    }

    public function onOpen(Player $who) : void {
        Main::getInstance()->getMarketManager()->addInventory($this);
        $this->setItems();
        parent::onOpen($who);
    }

    public function onClose(Player $who) : void {
        Main::getInstance()->getMarketManager()->removeInventory($this);
        parent::onClose($who);
    }

    abstract public function setItems() : void;
}