<?php

declare(strict_types=1);

namespace core\managers\safe;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;

class Safe {

    private string $nick;
    private string $description;

    private int $safeId;

    private Item $pattern;

    /** @var Item[] */
    private array $items;

    public function __construct(string $nick, string $description, Item $pattern, int $safeId, array $items) {
        $this->nick = $nick;
        $this->description = $description;
        $this->pattern = $pattern;
        $this->safeId = $safeId;
        $this->items = $items;
    }

    public function getName() : string {
        return $this->nick;
    }

    public function setName(string $name) : void {
        $this->nick = $name;
    }

    public function getDescription() : string {
        return $this->description;
    }

    public function setDescription(string $description) : void {
        $this->description = $description;
    }

    public function getPattern() : Item {
        return $this->pattern;
    }

    public function setPattern(Item $item) : void {
        $this->pattern = $item;
    }

    public function getSafeId() : int {
        return $this->safeId;
    }

    public function getItems() : array {
        return $this->items;
    }

    public function setItem(int $slot, Item $item) : void {
        if($item->getId() === 0) {
            $this->removeItemFromSafe($slot);
            return;
        }

        $this->items[$slot] = $item;
    }

    public function getItemFromSafe(int $slot) : Item {
        return $this->items[$slot] ?? VanillaBlocks::AIR()->asItem();
    }

    public function removeItemFromSafe(int $slot) : void {
        unset($this->items[$slot]);
    }

    public function clearItems() : void {
        $this->items = [];
    }
}