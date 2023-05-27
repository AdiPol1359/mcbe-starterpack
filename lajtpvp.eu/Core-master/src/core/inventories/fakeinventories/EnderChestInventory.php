<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use pocketmine\item\Item;
use pocketmine\player\Player;

class EnderChestInventory extends FakeInventory {

    public function __construct(private ?Player $player) {
        parent::__construct("EnderChest");
    }

    public function setItems() : void {
        if($this->player)
            $this->setContents($this->player->getEnderInventory()->getContents(true));
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        return false;
    }

    public function onClose(Player $who) : void {
        $who->getEnderInventory()->setContents($this->getContents(true));
        parent::onClose($who);
    }
}