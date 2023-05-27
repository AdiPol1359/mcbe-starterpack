<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\market;

use core\Main;
use core\utils\SoundUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class PlayerOffersInventory extends MarketInventory {

    private array $offers = [];

    public function __construct(private string $playerName) {
        parent::__construct( "§8Oferty gracza: §l§e" . $playerName. "§r§8!");
    }

    public function setItems() : void {
        $itemFactory = ItemFactory::getInstance();
        $this->clearAll();
        $this->fill();

        $slot = 11;

        foreach(Main::getInstance()->getMarketManager()->getPlayerOffers($this->playerName) as $offer) {
            $this->setItem($slot++, $offer->getGUIItem());
            $this->offers[] = $offer;
        }

        for(; $slot <= 15; $slot++)
            $this->setItem($slot, $itemFactory->get(ItemIds::AIR));
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== ItemIds::STAINED_GLASS_PANE)
            SoundUtil::addSound([$player], $this->holder, "random.click");

        foreach($this->offers as $offer) {
            if($offer->getGUIItem()->equalsExact($sourceItem)) {
                Main::getInstance()->getMarketManager()->handleClickOffer($player, $offer);
                $this->unClickItem($player);
                return true;
            }
        }

        $this->unClickItem($player);
        return true;
    }
}