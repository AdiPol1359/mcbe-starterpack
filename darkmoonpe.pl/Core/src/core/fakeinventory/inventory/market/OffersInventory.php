<?php

namespace core\fakeinventory\inventory\market;

use core\manager\managers\market\MarketManager;
use core\manager\managers\PacketManager;
use core\manager\managers\SoundManager;
use pocketmine\item\Item;
use pocketmine\Player;

class OffersInventory extends MarketInventory {

    private int $page;
    private array $offers = [];
    private Item $previousPageItem;
    private Item $nextPageItem;

    public function __construct(Player $player) {
        parent::__construct($player, "§l§9RYNEK");

        $this->previousPageItem = Item::get(Item::WOOL, 14)->setCustomName("§l§aPOPRZEDNIA STRONA");
        $this->nextPageItem = Item::get(Item::WOOL, 5)->setCustomName("§l§cNASTEPNA STRONA");
    }

    public function onOpen(Player $who) : void {
        $this->page = 1;
        $this->offers[$this->page] = [];
        parent::onOpen($who);
    }

    public function setItems() : void {
        $this->clearAll();
        $this->updateOffers();

        if(!isset($this->offers[$this->page]))
            $this->page = max(1, $this->page - 1);

        if(isset($this->offers[$this->page])) {
            foreach ($this->offers[$this->page] as $offer)
                $this->setItem($this->firstEmpty(), $offer->getGUIItem(), true, false);
        }

        if(isset($this->offers[$this->page - 1]))
            $this->setItem($this->getSize() - 2, $this->previousPageItem);

        if(isset($this->offers[$this->page + 1]))
            $this->setItem($this->getSize() - 1, $this->nextPageItem);

        $this->fillBars();
    }

    public function updateOffers() : void {
        $offers = [
            1 => []
        ];
        $page = 1;

        foreach(MarketManager::getOffers() as $offer) {
            if(count($offers[$page]) >= ($this->getSize() - 9))
                $page++;

            if(!isset($offers[$page]))
                $offers[$page] = [];

            $offers[$page][] = $offer;
        }

        $this->offers = $offers;
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        if($sourceItem->equalsExact($this->previousPageItem)) {
            $this->page--;
            $this->setItems();
            PacketManager::unClickButton($player);
            return true;
        }

        if($sourceItem->equalsExact($this->nextPageItem)) {
            $this->page++;
            $this->setItems();
            PacketManager::unClickButton($player);
            return true;
        }

        foreach($this->offers[$this->page] as $offer) {
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