<?php

namespace core\util\utils;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ListTag;

class ItemUtil {

    public static function getItemFromConfig(string $fullItemData) : Item {
        $fullItemData = explode(';', $fullItemData);

        $itemData = explode(':', $fullItemData[0]);
        $itemId = is_numeric($itemData[0]) ? (int) $itemData[0] : self::getItemIdByName($itemData[0]);
        $itemDamage = isset($itemData[1]) ? (int) $itemData[1] : 0;
        $itemCount = isset($itemData[2]) ? (int) $itemData[2] : 1;

        $item = Item::get($itemId, $itemDamage);
        $item->setCount($itemCount);

        if(isset($fullItemData[1]) && $fullItemData[1] != "")
            $item->setCustomName($fullItemData[1]);

        if(isset($fullItemData[2])) {
            for($i = 2; $i < count($fullItemData); $i++) {
                $enchData = explode(':', $fullItemData[$i]);
                $enchantment = is_numeric($enchData[0]) ? Enchantment::getEnchantment((int) $enchData[0]) : Enchantment::getEnchantmentByName($enchData[0]);
                $item->addEnchantment(new EnchantmentInstance($enchantment, (int) $enchData[1]));
            }
        }
        return $item;
    }

    public static function getItemIdByName(string $name) : ?int {
        $const = Item::class . "::" . strtoupper($name);
        
        if(defined($const))
            return constant($const);

        return null;
    }

    public static function itemFromString(string $fullItemData) : Item {

        $data = json_decode($fullItemData, true);

        $item = Item::get($data["id"], $data["damage"], $data["count"]);

        if(!empty($data["compoundTag"]))
            $item->setCompoundTag(base64_decode($data["compoundTag"]));

        if($data["customName"] !== "")
            $item->setCustomName($data["customName"]);

        if(!empty($data["lore"]))
            $item->setLore($data["lore"]);

        if(!empty($data["enchants"])) {
            foreach($data["enchants"] as $enchant => $level)
                $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($enchant), $level));
        }

        return $item;
    }

    public static function itemToString(Item $item) : string {
        $data = [];

        $data["id"] = $item->getId();
        $data["damage"] = $item->getDamage();
        $data["count"] = $item->getCount();

        $data["customName"] = $item->getCustomName();

        $data["lore"] = [];
        $data["enchants"] = [];
        $data["compoundTag"] = base64_encode($item->getCompoundTag());

        foreach($item->getLore() as $lore)
            $data["lore"][] = $lore;

        foreach($item->getEnchantments() as $enchantment)
            $data["enchants"][$enchantment->getId()] = $enchantment->getLevel();

        return json_encode($data);
    }

    public static function addItemGlow(Item $item) : void {

        $namedTag = $item->getNamedTag();

        if($namedTag->hasTag("ench"))
            return;

        $namedTag->setTag(new ListTag("ench", []));
    }
}
