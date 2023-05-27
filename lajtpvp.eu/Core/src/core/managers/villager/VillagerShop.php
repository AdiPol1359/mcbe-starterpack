<?php

declare(strict_types=1);

namespace core\managers\villager;

use pocketmine\item\Item;
use pocketmine\world\Position;

class VillagerShop {

    private int $id;
    private string $name;

    /** @var Item[] */
    private array $items;

    private Position $position;

    public function __construct(int $id, string $name, array $items, Position $position) {
        $this->id = $id;
        $this->name = $name;
        $this->items = $items;
        $this->position = $position;
    }

    public function getId() : int {
        return $this->id;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getItems() : array {
        return $this->items;
    }

    public function getPosition() : Position {
        return $this->position;
    }

    public function addItem(int $slot, int $cost, Item $item) : void {
        $namedTag = $item->getNamedTag();

        if(!$namedTag->getTag("shopSlot"))
            $namedTag->setInt("shopSlot", $slot);

        if(!$namedTag->getTag("costItem"))
            $namedTag->setInt("costItem", $cost);

        $this->items[$slot] = $item;
    }

    public function removeItem(int $slot) : void {
        unset($this->items[$slot]);
    }

    public function getEmptySlot() : ?int {
        for($i = 0; $i <= 53; $i++) {
            if(!isset($this->items[$i]))
                return $i;
        }

        return null;
    }
}