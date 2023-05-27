<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\drop;

use core\inventories\FakeInventory;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;

class MainDropInventory extends FakeInventory {

    public function __construct() {
        parent::__construct("§l§eDROP");
    }

    public function setItems() : void{
        $this->fill();

        $cobblex = VanillaBlocks::MOSSY_COBBLESTONE()->asItem();
        $cobblex->setCustomName("§l§eDROP Z COBBLEXA");

        $stone = VanillaBlocks::STONE()->asItem();
        $stone->setCustomName("§l§eDROP Z STONE");

        $premiumcase = VanillaBlocks::CHEST()->asItem();
        $premiumcase->setCustomName("§l§eDROP Z PREMIUMCASE");

        $this->setItemAt(2, 2, $cobblex);
        $this->setItemAt(5, 2, $stone);
        $this->setItemAt(8, 2, $premiumcase);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($slot === 10)
            $this->changeInventory($player, (new DropCobblexInventory()));

        if($slot === 13)
            $this->changeInventory($player, (new DropStoneInventory($player)));

        if($slot === 16)
            $this->changeInventory($player, (new DropPremiumCaseInventory()));

        $this->unClickItem($player);
        return true;
    }
}