<?php

namespace core\manager\managers\market;

use core\fakeinventory\inventory\market\MarketInventory;
use core\Main;
use core\manager\BaseManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\InventoryUtil;
use core\util\utils\ItemUtil;
use core\util\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\Player;
use SQLite3;

class MarketManager extends BaseManager {

    /** @var Offer[] */
    private static array $offers = [];
    /** @var MarketInventory[] */
    private static array $inventories = [];
    private static SQLite3 $db;

    public static function init() : void {
        self::$db = Main::getDb();

        Main::getDb()->query("CREATE TABLE IF NOT EXISTS offers (owner TEXT, price INT, item TEXT)");
    }

    public static function load() : void {
        $db = self::$db->query("SELECT * FROM offers");

        while($row = $db->fetchArray(SQLITE3_ASSOC))
            self::createOffer($row["owner"], $row["price"], ItemUtil::itemFromString($row["item"]));
    }

    public static function save() : void {
        self::$db->query("DELETE FROM offers");

        foreach(self::$offers as $offer)
            self::$db->query("INSERT INTO offers (owner, price, item) VALUES ('" . $offer->getOwner() . "', '" . $offer->getPrice() . "', '" . ItemUtil::itemToString($offer->getItem()) . "')");
    }

    /**
     * @return Offer[]
     */
    public static function getOffers() : array {
        return self::$offers;
    }

    public static function createOffer(string $owner, float $price, Item $item) : void {
        self::$offers[] = new Offer($owner, $price, $item);
    }

    public static function removeOffer(Offer $offer) : void {
        $key = array_search($offer, self::$offers);

        if(isset(self::$offers[$key])) {
            unset(self::$offers[$key]);
            self::updateInventories();
        }
    }

    public static function updateInventories() : void {
        foreach(self::$inventories as $inventory)
            $inventory->setItems();
    }

    public static function getPlayerOffers(string $name) : array {
        $offers = [];

        foreach(self::$offers as $offer) {
            if($offer->getOwner() === $name)
                $offers[] = $offer;
        }

        return $offers;
    }

    public static function addInventory(MarketInventory $inventory) : void {
        if(!in_array($inventory, self::$inventories))
            self::$inventories[] = $inventory;
    }

    public static function removeInventory(MarketInventory $inventory) : void {
        $key = array_search($inventory, self::$inventories);

        if(isset(self::$inventories[$key]))
            unset(self::$inventories[$key]);
    }

    public static function handleClickOffer(Player $player, Offer $offer) : void {

        $offerItem = $offer->getItem();

        if($player->getName() !== $offer->getOwner()) {

            $user = UserManager::getUser($player->getName());

            if($user->getPlayerMoney() < $offer->getPrice()){
                $player->doCloseInventory();
                $player->sendMessage(MessageUtil::format("Brakuje ci §l§9".abs($offer->getPrice() - $user->getPlayerMoney())."§r§7zl aby moc kupic ten przedmiot!"));
                return;
            }

            $user->reducePlayerMoney($offer->getPrice());

            $displayName = "§9" . ($offerItem->getCustomName() === "" ? $offerItem->getName() : $offerItem->getCustomName()) . " §r§7x§9§l" . $offerItem->getCount() . "§r";

            $player->sendMessage(MessageUtil::format("Zakupiles " . $displayName));

            $owner = $player->getServer()->getPlayerExact($offer->getOwner());

            $sellerUser = UserManager::getUser($offer->getOwner());
            $sellerUser->addPlayerMoney($offer->getPrice());

            if ($owner !== null)
                $owner->sendMessage(MessageUtil::format("Gracz §l§9" . $player->getName() . "§r§7 zakupil od Ciebie §l§9" . $displayName . "§r§7 za §l§9".$offer->getPrice()."§r§7zl!"));
        } else
            $player->sendMessage(MessageUtil::format("Usunieto oferte!"));

        self::removeOffer($offer);

        InventoryUtil::addItem($offerItem = $offer->getItem(), $player);

    }

    public static function getMaxPlayerOfferCount(Player $player) : int{

        $count = ConfigUtil::MAX_PLAYER_OFFER;

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG."offer.vip"))
            $count = ConfigUtil::MAX_VIP_OFFER;

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG."offer.svip"))
            $count = ConfigUtil::MAX_SVIP_OFFER;

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG."offer.sponsor"))
            $count = ConfigUtil::MAX_SPONSOR_OFFER;

        return $count;
    }
}