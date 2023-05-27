<?php

declare(strict_types=1);

namespace core\items\custom;

use core\utils\MessageUtil;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;

class TerrainAxe extends CustomItem {

    public function __construct(){
        parent::__construct(ItemIds::WOODEN_AXE, 0, VanillaItems::WOODEN_AXE()->getName(), "§l§8» §eSiekiera §8«", [
            " ",
            MessageUtil::format("Kliknij lewym przciskiem aby zaznaczyc §epierwsza §7pozycje!"),
            MessageUtil::format("Kliknij prawym przciskiem aby zaznaczyc §edruga §7pozycje!")
        ], []
        );
    }
}