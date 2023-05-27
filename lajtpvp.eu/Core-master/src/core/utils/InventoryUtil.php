<?php

declare(strict_types=1);

namespace core\utils;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Armor;
use pocketmine\item\Axe;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\FishingRod;
use pocketmine\item\Hoe;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\Pickaxe;
use pocketmine\item\Shovel;
use pocketmine\item\Sword;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\Server;

final class InventoryUtil {

    private function __construct() {}

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
                    $player->getWorld()->dropItem($player->getPosition(), $itemStack);
            }

            $player->getWorld()->dropItem($player->getPosition(), $item->setCount((int) $rest));
        }
    }

    public static function removeItem(Player $player, Item ...$slots) : array{

        /** @var Item[] $itemSlots */
        /** @var Item[] $slots */

        $itemSlots = [];
        foreach($slots as $slot){
            if(!$slot->isNull()){
                $itemSlots[] = clone $slot;
            }
        }

        for($i = 0, $size = $player->getInventory()->getSize(); $i < $size; ++$i){
            $item = $player->getInventory()->getItem($i);
            if($item->isNull())
                continue;

            foreach($itemSlots as $index => $slot){
                if($slot->equals($item, !$slot->hasAnyDamageValue(), $slot->hasNamedTag()) && !$item->hasEnchantments()){
                    $amount = min($item->getCount(), $slot->getCount());
                    $slot->setCount($slot->getCount() - $amount);
                    $item->setCount($item->getCount() - $amount);
                    $player->getInventory()->setItem($i, $item);
                    if($slot->getCount() <= 0){
                        unset($itemSlots[$index]);
                    }
                }
            }

            if(count($itemSlots) === 0)
                break;
        }

        return $itemSlots;
    }

    /**
     * @param Player $player
     * @param Item[] $items
     * @return bool
     */
    public static function containItems(Player $player, array $items) : bool{

        $results = [];

        foreach($items as $item) {
            if(!isset($results[$item->getName()]))
                $results[$item->getName()] = false;

            $count = max(1, $item->getCount());
            $checkDamage = !$item->hasAnyDamageValue();
            $checkTags = $item->hasNamedTag();
            foreach($player->getInventory()->getContents() as $i) {
                if($item->equals($i, $checkDamage, $checkTags)) {
                    if($i->hasEnchantments())
                        continue;

                    $count -= $i->getCount();
                    if($count <= 0)
                        $results[$item->getName()] = true;
                }
            }
        }

        $result = false;

        if(count(array_unique($results)) === 1)
            $result = current($results);

        return $result;
    }

    public static function getItemsFromArray(array $items) : array {

        $resultItems = [];

        foreach($items as $slot => $item) {

            $itemFactory = ItemFactory::getInstance()->get($item["id"], $item["damage"], $item["count"]);

            if(isset($item["customName"]))
                $itemFactory->setCustomName($item["customName"]);

            if(isset($item["lore"]))
                $itemFactory->setLore($item["lore"]);

            if(isset($item["enchantments"])) {
                foreach($item["enchantments"] as $enchantmentId => $enchantmentLevel)
                    $itemFactory->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId($enchantmentId), $enchantmentLevel));
            }

            $resultItems[$slot] = $item;
        }

        return $resultItems;
    }

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
                "POWER" => 3,
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

            if($item->getId() === ItemIds::DIAMOND_BOOTS || $item->getId() === ItemIds::IRON_BOOTS|| $item->getId() === ItemIds::CHAIN_BOOTS || $item->getId() === ItemIds::GOLD_BOOTS || $item->getId() === ItemIds::LEATHER_BOOTS)
                $array["FEATHER_FALLING"] = 2;

            return $array;
        }

        return [];
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
                return ItemFactory::getInstance()->get($item->offsetGet("id"), $item->offsetGet("Damage"), $item->offsetGet("Count"));
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

        $namedTag->setTag("Inventory", new ListTag($values, NBT::TAG_Compound));

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

        $namedTag->setTag("Inventory", new ListTag($values, NBT::TAG_Compound));

        Server::getInstance()->saveOfflinePlayerData($nick, $namedTag);
    }

    public static function getOfflinePlayerItems(string $nick) : array {

        /** @var Item[] $items */
        $items = [];
        $itemFactory = ItemFactory::getInstance();

        $namedTag = Server::getInstance()->getOfflinePlayerData($nick);

        if(!$namedTag)
            return [];

        foreach($namedTag->getTag("Inventory") as $value => $item) {
            $baseItem = $itemFactory->get($item->offsetGet("id"), $item->offsetGet("Damage"), $item->offsetGet("Count"));

            if($item->offsetGet("tag") !== null){
                foreach($item->offsetGet("tag") as $type => $values){

                    if($type === "ench"){
                        foreach($values as $enchantment)
                            $baseItem->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId($enchantment->offsetGet("id")), $enchantment->offsetGet("lvl")));
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

    public static function getOfflinePlayerEnderChestItems(string $nick) : array {

        /** @var Item[] $items */
        $items = [];

        $namedTag = Server::getInstance()->getOfflinePlayerData($nick);

        if(!$namedTag)
            return [];

        foreach($namedTag->getTag("EnderChestInventory") as $value => $item) {
            if($item->offsetGet("Slot") < 100)
                continue;

            $baseItem = ItemFactory::getInstance()->get($item->offsetGet("id"), $item->offsetGet("Damage"), $item->offsetGet("Count"));

            if($item->offsetGet("tag") !== null){
                foreach($item->offsetGet("tag") as $type => $values){

                    if($type === "ench"){
                        foreach($values as $enchantment)
                            $baseItem->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId($enchantment->offsetGet("id")), $enchantment->offsetGet("lvl")));
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

            $items[$item->offsetGet("Slot") - 100] = $baseItem;
        }

        return $items;
    }

    public static function getOfflinePlayerArmorItems(string $nick) : array {

        /** @var Item[] $items */
        $items = [];

        $namedTag = Server::getInstance()->getOfflinePlayerData($nick);

        if(!$namedTag)
            return [];

        foreach($namedTag->getTag("Inventory") as $value => $item) {
            if($item->offsetGet("Slot") < 100)
                continue;

            $baseItem = ItemFactory::getInstance()->get($item->offsetGet("id"), $item->offsetGet("Damage"), $item->offsetGet("Count"));

            if($item->offsetGet("tag") !== null){
                foreach($item->offsetGet("tag") as $type => $values){

                    if($type === "ench"){
                        foreach($values as $enchantment)
                            $baseItem->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId($enchantment->offsetGet("id")), $enchantment->offsetGet("lvl")));
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

            $items[$item->offsetGet("Slot") - 100] = $baseItem;
        }

        return $items;
    }
}