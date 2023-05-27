<?php

declare(strict_types=1);

namespace core\items\custom;

use core\utils\MessageUtil;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;

class Crowbar extends CustomItem {

    public function __construct() {
        parent::__construct(ItemIds::STICK, 0, VanillaItems::STICK()->getName(), "§l§8» §eLOM §8«", [" ", MessageUtil::format("Otworz zablokowany sejf aby go odblokowac§7!")], []);
    }
}