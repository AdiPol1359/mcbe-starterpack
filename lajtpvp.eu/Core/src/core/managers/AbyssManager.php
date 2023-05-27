<?php

declare(strict_types=1);

namespace core\managers;

use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;

class AbyssManager {

    /** @var Item[] */
    private array $items = [];

    /** @var ItemEntity[] */
    private array $itemEntities = [];

    public bool $opened = false;

    public function addItem(Item $item) : void {

        $count = $item->getCount();

        foreach($this->items as $key => $abyssItem) {

            if($count <= 0)
                return;

            if($item->equals($abyssItem) && $abyssItem->getCount() < 64) {
                for($i = $abyssItem->getCount(); $i < $item->getMaxStackSize(); $i++) {
                    if($count <= 0)
                        break;

                    $count--;
                }

                if($count < $item->getCount())
                    $abyssItem->setCount($abyssItem->getCount() + ($item->getCount() - $count));
            }
        }

        if($count > 0)
            $this->items[] = $item->setCount($count);
    }

    public function removeItem(Item $item) : void {
        $count = $item->getCount();

        foreach($this->items as $slot => $abyssItem) {
            if($item->equals($abyssItem)) {
                for($i = $abyssItem->getCount(); $i > 0; $i--) {
                    if($count <= 0)
                        break;

                    $count--;
                }

                if($count < $item->getCount())
                    $abyssItem->setCount($abyssItem->getCount() - ($item->getCount() - $count));
            }
        }
    }

    public function clearAll() : void {
        $this->items = [];
        $this->itemEntities = [];
    }

    public function removeItemBySlot(int $slot) : void {
        unset($this->items[$slot]);
    }

    public function checkItem(int $slot, Item $item) : bool {
        return isset($this->items[$slot]) && $item->equals($this->items[$slot]);
    }

    public function getItems() : array {
        return $this->items;
    }

    public function addItemEntity(ItemEntity $itemEntity) : void {
        $this->itemEntities[$itemEntity->getId()] = $itemEntity;
    }

    public function removeItemEntity(int $id) : void {
        unset($this->itemEntities[$id]);
    }

    public function getItemEntity(int $id) : ?ItemEntity {
        return $this->itemEntities[$id] ?? null;
    }

    public function getItemEntities() : array {
        return $this->itemEntities;
    }

    public function isOpened() : bool {
        return $this->opened;
    }

    public function setOpened(bool $val = true) : void {
        $this->opened = $val;
    }
}