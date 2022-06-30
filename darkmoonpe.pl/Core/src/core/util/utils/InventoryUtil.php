<?php

namespace core\util\utils;

use pocketmine\item\Armor;
use pocketmine\item\Axe;
use pocketmine\item\Bow;
use pocketmine\item\ChainBoots;
use pocketmine\item\DiamondBoots;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\FishingRod;
use pocketmine\item\GoldBoots;
use pocketmine\item\Hoe;
use pocketmine\item\IronBoots;
use pocketmine\item\Item;
use pocketmine\item\LeatherBoots;
use pocketmine\item\Pickaxe;
use pocketmine\item\Shovel;
use pocketmine\item\Sword;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\Server;

class InventoryUtil {

    public static function instanceofTool(Item $item) : bool {
        return $item instanceof Pickaxe || $item instanceof Axe || $item instanceof Hoe || $item instanceof Shovel;
    }

    public static function getEnchantmentsForItem(Item $item) : array {

        if(self::instanceofTool($item)) {
            return [
                "EFFICIENCY" => 5,
                "FORTUNE" => 3,
                "UNBREAKING" => 3,
                "SILK_TOUCH" => 1
            ];
        }

        if($item instanceof Sword) {
            return [
                "SHARPNESS" => 3,
                "UNBREAKING" => 3,
                "KNOCKBACK" => 2,
                "FIRE_ASPECT" => 1,
            ];
        }

        if($item instanceof Bow) {
            return [
                "POWER" => 4,
                "UNBREAKING" => 3,
                "FLAME" => 1
            ];
        }

        if($item instanceof FishingRod) {
            return [
                "UNBREAKING" => 3
            ];
        }

        if($item instanceof Armor) {

            $array = [
                "PROTECTION" => 3,
                "UNBREAKING" => 2
            ];

            if($item instanceof DiamondBoots || $item instanceof IronBoots || $item instanceof ChainBoots || $item instanceof GoldBoots || $item instanceof LeatherBoots)
                $array["FEATHER_FALLING"] = 2;

            return $array;
        }

        return [];
    }

    public static function addItem(Item $item, Player $player) : void{
        if($player->getInventory()->canAddItem($item))
            $player->getInventory()->addItem($item);
        else {
            $count = $item->getCount();

            $stacks = floor($count / 64);
            $rest = $count - ($stacks * 64);

            for($i = 1; $i <= $stacks; $i++) {
                $itemStack = $item->setCount(64);

                if($player->getInventory()->canAddItem($itemStack))
                    $player->getInventory()->addItem($itemStack);
                else
                    $player->getLevel()->dropItem($player->asVector3(), $itemStack);
            }

            $player->getLevel()->dropItem($player->asVector3(), $item->setCount($rest));
        }
    }

    public static function hasItem(Player $player, Item $item, bool $exact = false) : bool{

        $founded = false;

        foreach($player->getInventory()->getContents() as $slot => $slotItem){
            if($exact){
                if($slotItem->equalsExact($item)) {
                    $founded = true;
                    break;
                }

                continue;
            }

            if($slotItem->equals($item)) {
                $founded = true;
                break;
            }
        }

        return $founded;
    }


    public static function findItemBySlot(string $nick, int $slot) : ?Item {

        $namedTag = Server::getInstance()->getOfflinePlayerData($nick);

        if(!$namedTag)
            return null;

        foreach($namedTag->getTag("Inventory") as $value => $item) {
            if($item->offsetGet("Slot") === $slot)
                return Item::get($item->offsetGet("id"), $item->offsetGet("Damage"), $item->offsetGet("Count"));
        }

        return null;
    }

    public static function addItemToSlot(string $nick, Item $item, int $slot) : void{
        $namedTag = Server::getInstance()->getOfflinePlayerData($nick);

        if(!$namedTag)
            return;

        $values = [];

        foreach($namedTag->getTag("Inventory") as $value => $inventoryItem) {
            if($inventoryItem->offsetGet("Slot") === $slot)
                continue;

            $values[] = $inventoryItem;
        }

        $values[] = $item->nbtSerialize($slot);

        $namedTag->setTag(new ListTag("Inventory", $values, NBT::TAG_Compound));

        Server::getInstance()->saveOfflinePlayerData($nick, $namedTag);
    }

    public static function removeItemFromSlot(string $nick, int $slot) : void{
        $namedTag = Server::getInstance()->getOfflinePlayerData($nick);

        if(!$namedTag)
            return;

        $values = [];

        foreach($namedTag->getTag("Inventory") as $value => $item) {
            if($item->offsetGet("Slot") === $slot)
                continue;

            $values[] = $item;
        }

        $namedTag->setTag(new ListTag("Inventory", $values, NBT::TAG_Compound));

        Server::getInstance()->saveOfflinePlayerData($nick, $namedTag);
    }

    public static function getOfflinePlayerItems(string $nick) : array {

        /** @var Item[] $items */
        $items = [];

        $namedTag = Server::getInstance()->getOfflinePlayerData($nick);

        if(!$namedTag)
            return [];

        foreach($namedTag->getTag("Inventory") as $value => $item) {
            $baseItem = Item::get($item->offsetGet("id"), $item->offsetGet("Damage"), $item->offsetGet("Count"));

            if($item->offsetGet("tag") !== null){
                foreach($item->offsetGet("tag") as $type => $values){

                    if($type === "ench"){
                        foreach($values as $enchantment)
                            $baseItem->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($enchantment->offsetGet("id")), $enchantment->offsetGet("lvl")));
                    }

                    if($type === "stattrack")
                        $baseItem->getNamedTag()->setInt("stattrack", $values->getValue());

                    $lore = [];

                    if($type === "display"){
                        foreach($values as $display) {
                            if($display instanceof ListTag) {
                                foreach($display as $loreKey => $loreValue)
                                    $lore[] = $loreValue->getValue();
                            }

                            if($display instanceof StringTag)
                                $baseItem->setCustomName($display->getValue());
                        }
                    }

                    $baseItem->setLore($lore);
                }
            }

            $items[$item->offsetGet("Slot")] = $baseItem;
        }

        return $items;
    }
}