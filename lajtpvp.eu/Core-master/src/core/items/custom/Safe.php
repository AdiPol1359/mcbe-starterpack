<?php

declare(strict_types=1);

namespace core\items\custom;

use core\inventories\FakeInventorySize;
use core\managers\safe\Safe as SafeM;
use core\utils\MessageUtil;

class Safe extends CustomItem {

    public function __construct(SafeM $safe) {
        $pattern = $safe->getPattern();

        parent::__construct($pattern->getId(), $pattern->getMeta(), $pattern->getName(), "§l§8» §eSEJF §8(§r§7#§e".$safe->getSafeId()."§l§8) §8«", [" ", MessageUtil::format("Wlasciciel§8: §e".$safe->getName()."§7!"), MessageUtil::format("Opis§8: §e".$safe->getDescription()), MessageUtil::format("Ilosc przedmiotow§8: §8(§e".count($safe->getItems())."§7/§e".FakeInventorySize::LARGE_CHEST."§8)")], []);

        $this->getNamedTag()->setInt("safeId", $safe->getSafeId());
    }
}