<?php

declare(strict_types=1);

namespace core\items\custom;

use core\utils\ItemUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\nbt\tag\CompoundTag;

abstract class CustomItem extends Item {

    protected $customName;
    protected $lore;
    protected $enchantments;

    public function __construct(int $id, int $meta = 0, string $name = "Unknown", string $customName = "", ?array $lore = [], ?array $enchant = [], bool $glow = true) {
        $this->customName = "§r".$customName;
        $this->lore = $lore;
        $this->enchantments = $enchant;

        $this->setCustomName($this->customName);

        if($this->lore) {
            $this->setLore($this->lore);
        }

        if($this->enchantments){
            foreach($this->enchantments as $enchantment) {
                $this->addEnchantment($enchantment);
            }
        }

        parent::__construct(new ItemIdentifier($id, $meta), $name);

        if($glow) {
            ItemUtil::addItemGlow($this);
        }
    }

    public function getCustomName() : string {
        return $this->customName;
    }

    public function getLore() : array {
        return $this->lore;
    }

    public function getEnchantments() : array {
        return $this->enchantments;
    }

    public function clearLore() : Item{
        $this->lore = [];
        $this->setLore([]);

        $namedTag = $this->getNamedTag();

        $compoundTag = CompoundTag::create();
        $values = [];

        foreach($namedTag->getTag("display") as $key => $value){
            if($value->getName() === "Lore")
                continue;

            $compoundTag->setTag($key, $value);
            //$values[] = $value;
        }

        $namedTag->setTag("display", $compoundTag);

        return $this;
    }

    public function __toItem() : Item {
        $item = ItemFactory::getInstance()->get($this->getId(), $this->getMeta());
        $item->setNamedTag($this->getNamedTag());

        $item->setCustomName($this->getCustomName());
        $item->setLore($this->getLore());

        foreach($this->getEnchantments() as $enchantment) {
            $item->addEnchantment($enchantment);
        }

        return $item;
    }
}