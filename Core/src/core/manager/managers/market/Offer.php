<?php

namespace core\manager\managers\market;

use pocketmine\item\Item;

class Offer {

    private string $owner;
    private float $price;
    private Item $item;

    public function __construct(string $owner, float $price, Item $item) {
        $this->owner = $owner;
        $this->price = $price;
        $this->item = $item;
    }

    public function getOwner() : string {
        return $this->owner;
    }

    public function getPrice() : float {
        return $this->price;
    }

    public function getItem() : Item {
        return $this->item;
    }

    public function getGUIItem() : Item {
        $item = clone $this->item;
        $item->setLore([
            " ",
            "§l§8» §r§7Cena: §l§9".$this->price."§r§7zl!",
            "§l§8» §r§7Sprzedawca: §l§9".$this->owner."§r§7!"
        ]);

        return $item;
    }
}