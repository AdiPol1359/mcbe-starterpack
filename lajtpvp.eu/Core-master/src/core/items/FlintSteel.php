<?php

declare(strict_types=1);

namespace core\items;

use pocketmine\block\Block;
use pocketmine\item\FlintSteel as PMFlintSteel;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class FlintSteel extends PMFlintSteel {

    public function __construct() {
        parent::__construct(new ItemIdentifier(ItemIds::FLINT_STEEL, 0), "Flint and Steel");
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : ItemUseResult {
        return ItemUseResult::NONE();
    }
}