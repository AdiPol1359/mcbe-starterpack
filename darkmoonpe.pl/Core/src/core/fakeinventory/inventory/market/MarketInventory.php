<?php

namespace core\fakeinventory\inventory\market;

use core\fakeinventory\FakeInventory;
use core\manager\managers\market\MarketManager;
use pocketmine\Player;

abstract class MarketInventory extends FakeInventory {

    public function __construct(Player $player, string $title = "FakeInventory", int $size = self::BIG) {
        parent::__construct($player, $title, $size);
    }

    public function onOpen(Player $who) : void {
        MarketManager::addInventory($this);
        $this->setItems();
        parent::onOpen($who);
    }

    public function onClose(Player $who) : void {
        MarketManager::removeInventory($this);
        parent::onClose($who);
    }

    abstract public function setItems() : void;
}