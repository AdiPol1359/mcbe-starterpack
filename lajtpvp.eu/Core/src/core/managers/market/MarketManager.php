<?php

namespace core\managers\market;

use core\inventories\fakeinventories\market\MarketInventory;
use core\Main;
use core\providers\ProviderInterface;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\InventoryUtil;
use core\utils\MessageUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class MarketManager {

    /** @var Offer[] */
    private array $offers = [];
    /** @var MarketInventory[] */
    private array $inventories = [];
    private ProviderInterface $provider;

    public function __construct(private Main $plugin) {
        $this->provider = $this->plugin->getProvider();
        $this->load();
    }

    public function load() : void {
        foreach($this->provider->getQueryResult("SELECT * FROM offers", true) as $row) {
            $this->createOffer($row["owner"], $row["price"], Item::jsonDeserialize(json_decode($row["item"], true)));
        }
    }

    public function save() : void {
        $this->provider->executeQuery("DELETE FROM offers");

        foreach($this->offers as $offer)
            $this->provider->executeQuery("INSERT INTO offers (owner, price, item) VALUES ('" . $offer->getOwner() . "', '" . $offer->getPrice() . "', '" . json_encode($offer->getItem()->jsonSerialize()) . "')");
    }

    /**
     * @return Offer[]
     */
    public function getOffers() : array {
        return $this->offers;
    }

    public function createOffer(string $owner, float $price, Item $item) : void {
        $this->offers[] = new Offer($owner, $price, $item);
    }

    public function removeOffer(Offer $offer) : void {
        foreach($this->offers as $key => $varOffer) {
            if($offer === $varOffer) {
                unset($this->offers[$key]);
                $this->updateInventories();
            }
        }
    }

    public function updateInventories() : void {
        foreach($this->inventories as $inventory)
            $inventory->setItems();
    }

    #[Pure] public function getPlayerOffers(string $name) : array {
        $offers = [];

        foreach($this->offers as $offer) {
            if($offer->getOwner() === $name)
                $offers[] = $offer;
        }

        return $offers;
    }

    public function addInventory(MarketInventory $inventory) : void {

        $founded = false;

        foreach($this->inventories as $key => $marketInventory) {
            if($marketInventory === $inventory)
                $founded = true;
        }

        if(!$founded)
            $this->inventories[] = $inventory;
    }

    public function removeInventory(MarketInventory $inventory) : void {
        foreach($this->inventories as $key => $marketInventory) {
            if($marketInventory === $inventory)
                unset($this->inventories[$key]);
        }
    }

    public function handleClickOffer(Player $player, Offer $offer) : void {
        $offerItem = $offer->getItem();
        $goldCount = 0;
        $itemFactory = ItemFactory::getInstance();

        if($player->getName() !== $offer->getOwner()) {

            foreach($player->getInventory()->getContents(false) as $slot => $invItem) {
                if($invItem->getId() === ItemIds::GOLD_INGOT)
                    $goldCount += $invItem->getCount();
            }

            if(!$player->getInventory()->contains(($item = $itemFactory->get(ItemIds::GOLD_INGOT, 0, $offer->getPrice())))){
                $player->removeCurrentWindow();
                $player->sendMessage(MessageUtil::format("Brakuje ci §e".abs($offer->getPrice() - $goldCount)."§r§7 zlota aby moc kupic ten przedmiot!"));
                return;
            }

            $player->getInventory()->removeItem($item);

            $displayName = "§e" . ($offerItem->getCustomName() === "" ? $offerItem->getName() : $offerItem->getCustomName()) . " §r§7x§e" . $offerItem->getCount() . "§r";

            $player->sendMessage(MessageUtil::format("Zakupiles " . $displayName));

            $owner = $player->getServer()->getPlayerExact($offer->getOwner());

            $sellerUser = $this->plugin->getUserManager()->getUser($offer->getOwner());
            $sellerBank = $sellerUser->getBankManager();

            $sellerBank->addBankStatus("gold", $offer->getPrice());

            $owner?->sendMessage(MessageUtil::format("Gracz §e" . $player->getName() . "§r§7 zakupil od Ciebie §e" . $displayName . "§r§7 za §e" . $offer->getPrice() . "§r§7 zlota!"));
        } else
            $player->sendMessage(MessageUtil::format("Usunieto oferte!"));

        $this->removeOffer($offer);

        InventoryUtil::addItem($offerItem = $offer->getItem(), $player);
    }

    public function getMaxPlayerOfferCount(Player $player) : int{

        $count = Settings::MAX_PLAYER_OFFER;

        if(PermissionUtil::has($player, Settings::$PERMISSION_TAG."offer.vip"))
            $count = Settings::MAX_VIP_OFFER;

        if(PermissionUtil::has($player, Settings::$PERMISSION_TAG."offer.svip"))
            $count = Settings::MAX_SVIP_OFFER;

        if(PermissionUtil::has($player, Settings::$PERMISSION_TAG."offer.sponsor"))
            $count = Settings::MAX_SPONSOR_OFFER;

        return $count;
    }
}