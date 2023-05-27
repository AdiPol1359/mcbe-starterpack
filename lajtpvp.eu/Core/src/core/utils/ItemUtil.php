<?php

declare(strict_types=1);

namespace core\utils;

use JetBrains\PhpStorm\Pure;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\Tool;

final class ItemUtil {

    private function __construct() {}

    public static function addItemGlow(Item $item) : Item {
        $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(-1)));
        return $item;
    }

    public static function getItemFromConfig(string $fullItemData) : Item {
        $fullItemData = explode(';', $fullItemData);

        $itemData = explode(':', $fullItemData[0]);
        $itemId = is_numeric($itemData[0]) ? (int) $itemData[0] : self::getItemIdByName($itemData[0]);
        $itemDamage = isset($itemData[1]) ? (int) $itemData[1] : 0;
        $itemCount = isset($itemData[2]) ? (int) $itemData[2] : 1;

        $item = ItemFactory::getInstance()->get($itemId, $itemDamage);
        $item->setCount($itemCount);

        if(isset($fullItemData[1]) && $fullItemData[1] != "")
            $item->setCustomName($fullItemData[1]);

        if(isset($fullItemData[2])) {
            for($i = 2; $i < count($fullItemData); $i++) {
                $enchData = explode(':', $fullItemData[$i]);
                $enchantment = is_numeric($enchData[0]) ? VanillaEnchantments::get((int) $enchData[0]) : VanillaEnchantments::fromString($enchData[0]);
                $item->addEnchantment(new EnchantmentInstance($enchantment, (int) $enchData[1]));
            }
        }
        return $item;
    }

    #[Pure] public static function getItemIdByName(string $name) : ?int {
        $const = Item::class . "::" . strtoupper($name);

        if(defined($const))
            return constant($const);

        return null;
    }

    public static function itemFromString(string $fullItemData) : Item {

        $data = json_decode($fullItemData, true);

        $item = Item::get($data["id"], $data["damage"], $data["count"]);

        //$item->setCompoundTag(base64_decode($data["namedTag"]));

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

        //$data["namedTag"] = base64_encode($item->getNamedTag());
        $data["id"] = $item->getId();
        $data["damage"] = $item->getDamage();
        $data["count"] = $item->getCount();

        $data["customName"] = $item->getCustomName();

        $data["lore"] = [];
        $data["enchants"] = [];

        foreach($item->getLore() as $lore)
            $data["lore"][] = $lore;

        foreach($item->getEnchantments() as $enchantment)
            $data["enchants"][$enchantment->getId()] = $enchantment->getLevel();

        return json_encode($data);
    }

    public static function getItemByString(string $itemStringData) : Item {

        $itemData = explode(";", $itemStringData);

        $item = ItemFactory::getInstance()->get($itemData[0], $itemData[1], $itemData[2]);

        if($itemData[3] !== "default")
            $item->setCustomName($itemData[3]);

        if($itemData[4] !== null) {
            $enchantments = explode("-", $itemData[4]);

            foreach($enchantments as $enchantment => $level)
                $item->addEnchantment(new EnchantmentInstance(StringToEnchantmentParser::getInstance()->parse($enchantment), (int)$level));
        }

        return $item;
    }

    public static function isRepairable(Item $item) : bool {
        return $item instanceof Tool || $item instanceof Armor;
    }
}