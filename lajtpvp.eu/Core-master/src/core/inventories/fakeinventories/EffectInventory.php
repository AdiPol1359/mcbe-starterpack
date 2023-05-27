<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryPatterns;
use core\inventories\FakeInventorySize;
use core\utils\LoreCreator;
use core\utils\Settings;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class EffectInventory extends FakeInventory {

    public function __construct() {
        parent::__construct("§l§eEFEKTY", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void {
        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_CORNERS);

        foreach(Settings::$EFFECT_LIST_DATA as $slot => $effectData) {
            $item = clone $effectData["item"];
            $item->setCustomName("§7[§8---===§7[ §e".$effectData["name"]."§7 ]§8===---§7]");

            $loreCreator = new LoreCreator($item->getCustomName(), [
                "",
                "§r§7Dostepnosc§8: ".($effectData["available"] ? "§aMozna kupic" : "§cNie mozna kupic"),
                "§r§7Koszt§8: §e".$effectData["cost"]."x emeraldow",
                "§r§7Czas trwania§8: §e3 minuty",
                ""
            ]);

            $loreCreator->alignLore();
            $item->setLore($loreCreator->getLore());

            $namedTag = $item->getNamedTag();
            $namedTag->setInt("effectCost", $effectData["cost"]);
            $namedTag->setInt("effectId", $effectData["effectId"]);
            $namedTag->setInt("effectLevel", $effectData["effectLevel"]);
            $namedTag->setInt("available", (int)$effectData["available"]);

            $this->setItem($slot, $item, true, true);
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $namedTag = $sourceItem->getNamedTag();

        if($namedTag->getTag("effectCost") && $namedTag->getTag("effectId") && $namedTag->getTag("effectLevel") && $namedTag->getTag("available")) {

            $cost = $namedTag->getInt("effectCost");
            $effectId = $namedTag->getInt("effectId");
            $effectLevel = $namedTag->getInt("effectLevel");
            $available = $namedTag->getInt("available");

            if($available) {
                $isOp = $player->getServer()->isOp($player->getName());
                if($player->getInventory()->contains(($item = ItemFactory::getInstance()->get(ItemIds::EMERALD, 0, $cost))) || $isOp) {
                    if(!$isOp) {
                        $player->getInventory()->removeItem($item);
                    }

                    if($effectId === -1 || $effectLevel === -1) {
                        $player->getEffects()->clear();
                    } else {
                        $player->getEffects()->add(new EffectInstance(EffectIdMap::getInstance()->fromId($effectId), 20 * 60 * 3, $effectLevel));
                    }
                }
            }
        }

        $this->unClickItem($player);
        return true;
    }
}