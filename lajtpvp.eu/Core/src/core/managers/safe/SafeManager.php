<?php

declare(strict_types=1);

namespace core\managers\safe;

use core\Main;
use core\utils\InventoryUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use core\items\custom\Safe as ItemSafe;

use core\managers\safe\Safe as SafeM;

class SafeManager {

    /** @var Safe[] */
    private array $safes = [];
    
    public function __construct(private Main $plugin) {
    }

    public function loadSafes() : void {
        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM safe", true) as $row) {
            $items = [];
            $itemJson = explode(";", $row["items"]);

            foreach($itemJson as $itemData) {
                if($itemData === "") {
                    continue;
                }

                $item = Item::jsonDeserialize(json_decode($itemData, true));
                $namedTag = $item->getNamedTag();

                if($namedTag->getInt("safeSlot", -1) === -1) {
                    continue;
                }

                $slot = $namedTag->getInt("safeSlot");
                $item->getNamedTag()->removeTag("safeSlot");
                $items[$slot] = $item;
            }

            $this->safes[] = new Safe($row["nick"], $row["description"], Item::jsonDeserialize(json_decode($row["pattern"] ?? [], true)), (int)$row["id"],$items ?? []);
        }
    }

    public function save() : void {
        foreach($this->safes as $safe) {
            $items = "";

            foreach($safe->getItems() as $slot => $item) {
                $item->getNamedTag()->setInt("safeSlot", $slot);
                $items .= json_encode($item->jsonSerialize()) . ";";
            }

            if(empty($this->plugin->getProvider()->getQueryResult("SELECT * FROM safe WHERE id = '".$safe->getSafeId()."'", true))) {
                $this->plugin->getProvider()->executeQuery("INSERT INTO safe (nick, id, description, pattern, items) VALUES ('" . $safe->getName() . "', '" . $safe->getSafeId() . "', '" . $safe->getDescription() . "', '" . json_encode($safe->getPattern()->jsonSerialize()) . "', '" . $items . "')");
            } else {
                $this->plugin->getProvider()->executeQuery("UPDATE safe SET description = '".$safe->getDescription()."', pattern = '".json_encode($safe->getPattern()->jsonSerialize())."', items = '".$items."' WHERE id = '".$safe->getSafeId()."'");
            }
        }
    }

    #[Pure] public function getSafeById(int $id) : ?Safe {
        foreach($this->safes as $safe) {
            if($safe->getSafeId() === $id)
                return $safe;
        }

        return null;
    }

    #[Pure] public function getRandomSafeId() : int {

        $highestId = 0;

        foreach($this->safes as $safe) {
            if($safe->getSafeId() > $highestId)
                $highestId = $safe->getSafeId();
        }

        return $highestId + 1;
    }

    public function addSafe(Player $player) : void {
        $this->safes[] = ($safeData = new SafeM($player->getName(), "BRAK", VanillaBlocks::CHEST()->asItem(), $this->getRandomSafeId(), []));

        $safe = new ItemSafe($safeData);

        InventoryUtil::addItem($safe, $player);
    }

    public function isSafe(Item $item) : bool {

        $namedTag = $item->getNamedTag();

        if($namedTag->getInt("safeId", -1) === -1)
            return false;

        if(!$this->getSafeById($namedTag->getInt("safeId")))
            return false;

        return true;
    }

    public function deleteSafe(int $id) : void {
        foreach($this->safes as $key => $safe) {
            if($safe->getSafeId() === $id) {
                unset($this->safes[$key]);
                return;
            }
        }
    }
}