<?php

declare(strict_types=1);

namespace core\utils;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

final class CustomItemUtil {

    public function __construct(
        private int $id,
        private int $meta = 0,
        private int $count = 1,
        private string $customName = "",
        private array $lore = [],
    ){}

    public function getId() : int {
        return $this->id;
    }

    public function getMeta() : int {
        return $this->meta;
    }

    public function getCount() : int {
        return $this->count;
    }

    public function getCustomName() : string {
        return $this->customName;
    }

    public function getLore() : array {
        return $this->lore;
    }

    public function setId(int $id) : void {
        $this->id = $id;
    }

    public function setMeta(int $meta) : void {
        $this->meta = $meta;
    }

    public function setCount(int $count) : void {
        $this->count = $count;
    }

    public function setCustomName(string $customName) : void {
        $this->customName = $customName;
    }

    public function setLore(array $lore) : void {
        $this->lore = $lore;
    }

    public function getItem() : Item {
        $item = ItemFactory::getInstance()->get($this->id, $this->meta, $this->count);
        $item->setCustomName($this->customName);
        $item->setLore($this->lore);

        return $item;
    }
}