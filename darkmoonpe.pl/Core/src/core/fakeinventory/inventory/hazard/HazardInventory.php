<?php

namespace core\fakeinventory\inventory\hazard;

use core\fakeinventory\FakeInventory;
use core\fakeinventory\inventory\hazard\roulette\MainRouletteInventory;
use core\manager\managers\hazard\HazardManager;
use core\manager\managers\PacketManager;
use core\manager\managers\SoundManager;
use pocketmine\item\Item;
use pocketmine\Player;

class HazardInventory extends FakeInventory {

    public function __construct(Player $player) {
        parent::__construct($player, "§l§9HAZARD", self::SMALL);

        $this->setItems();
    }

    public function setItems() : void {

        $this->fillBars();

        $emptySlots = [10, 11, 12, 13, 14, 15, 16];

        foreach($emptySlots as $slot)
            $this->setItem($slot, Item::get(Item::AIR));

        $lastSlot = 10;
        $hazardItem = Item::get(Item::BUCKET);

        foreach(HazardManager::getHazardGames() as $hazardGameName => $hazardGame) {
            if($hazardGame->isLocked())
                $hazardItem->setDamage(10);
            else
                $hazardItem->setDamage(8);

            $hazardItem->setCustomName("§r§9§l".strtoupper($hazardGameName));

            $hazardItem->getNamedTag()->setString("hazardGame", $hazardGameName);

            $this->setItem($lastSlot, $hazardItem);
            $lastSlot++;
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        if($sourceItem->getNamedTag()->hasTag("hazardGame")){

            $hazardGameName = $sourceItem->getNamedTag()->getString("hazardGame");

            switch($hazardGameName) {
                case "Ruletka":
                    (new MainRouletteInventory($player))->openFor([$player]);
                    break;
            }
        }

        PacketManager::unClickButton($player);
        return true;
    }
}