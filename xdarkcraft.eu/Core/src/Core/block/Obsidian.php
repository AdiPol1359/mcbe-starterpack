<?php

namespace Core\block;

use pocketmine\block\Obsidian as PMObsidian;

class Obsidian extends PMObsidian {

	public function getBlastResistance() : float {
		return 60;
	}
}