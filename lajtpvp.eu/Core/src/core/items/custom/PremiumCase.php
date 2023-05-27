<?php

declare(strict_types=1);

namespace core\items\custom;

use core\utils\MessageUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemIds;

class PremiumCase extends CustomItem {

    public function __construct() {
        parent::__construct(ItemIds::CHEST, 0, VanillaBlocks::CHEST()->asItem()->getName(), "§l§8» §ePREMIUMCASE §8«", [" ", MessageUtil::format("Postaw aby otrzymac nagrode!"), MessageUtil::format("Zakupic mozna ze strony serwera §eLajtPVP.PL")]);
    }
}