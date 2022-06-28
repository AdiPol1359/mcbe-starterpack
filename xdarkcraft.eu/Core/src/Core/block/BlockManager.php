<?php

namespace Core\block;

use pocketmine\block\BlockFactory;

class BlockManager {
	public static function init() : void {
	 BlockFactory::registerBlock(new Obsidian(), true);
	 BlockFactory::registerBlock(new Beacon(), true);
	}
}