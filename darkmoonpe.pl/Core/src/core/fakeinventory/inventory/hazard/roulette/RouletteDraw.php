<?php

namespace core\fakeinventory\inventory\hazard\roulette;

use core\fakeinventory\FakeInventory;
use core\manager\managers\PacketManager;
use core\manager\managers\SoundManager;
use core\task\tasks\RouletteDrawTask;
use pocketmine\item\Item;
use pocketmine\Player;

class RouletteDraw extends FakeInventory {

    public function __construct() {
        parent::__construct(null, "§9§lLosowanie§8...", self::SMALL);
        $this->setItems();
    }

    private function setItems() : void {
        for($i = 0; $i < $this->getSize(); $i++)
            $this->setItem($i, Item::get(Item::IRON_BARS)->setCustomName(" "));

        $this->setItem(4, Item::get(Item::HOPPER)->setCustomName(" "));
        $this->setItem(22, Item::get(Item::LEVER)->setCustomName(" "));

        for($i = 9; $i <= 17; $i++)
            $this->setItem($i, Item::get(Item::AIR));
    }

    public function onOpen(Player $who) : void {
        parent::onOpen($who);
        RouletteDrawTask::getInstance()->addPlayer($who);
    }

    public function onClose(Player $who) : void {
        parent::onClose($who);
        RouletteDrawTask::getInstance()->removePlayer($who);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        PacketManager::unClickButton($player);
        return true;
    }
}