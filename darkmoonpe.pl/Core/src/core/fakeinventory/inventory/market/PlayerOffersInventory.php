<?php

namespace core\fakeinventory\inventory\market;

use core\manager\managers\market\MarketManager;
use core\manager\managers\PacketManager;
use core\manager\managers\SoundManager;
use pocketmine\item\Item;
use pocketmine\Player;

class PlayerOffersInventory extends MarketInventory {

    private string $playerName;
    private array $offers = [];

    public function __construct(Player $player, string $playerName) {
        parent::__construct($player, "§8Oferty gracza: §l§9" . $playerName. "§r§8!");
        $this->playerName = $playerName;
    }

    public function setItems() : void {
        $this->clearAll();
        $this->fillBars();

        $slot = 11;

        foreach(MarketManager::getPlayerOffers($this->playerName) as $offer) {
            $this->setItem($slot++, $offer->getGUIItem(), true, false);
            $this->offers[] = $offer;
        }

        for(; $slot <= 15; $slot++)
            $this->setItem($slot, Item::get(Item::AIR));
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        foreach($this->offers as $offer) {
            if($offer->getGUIItem()->equalsExact($sourceItem)) {
                MarketManager::handleClickOffer($player, $offer);
                PacketManager::unClickButton($player);
                return true;
            }
        }

        PacketManager::unClickButton($player);
        return true;
    }
}