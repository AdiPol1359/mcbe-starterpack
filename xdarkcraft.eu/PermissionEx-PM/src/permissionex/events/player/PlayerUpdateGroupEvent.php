<?php

declare(strict_types=1);

namespace permissionex\events\player;

use pocketmine\Player;
use pocketmine\event\player\PlayerEvent;

class PlayerUpdateGroupEvent extends PlayerEvent {
	
	public function __construct(Player $player) {
		$this->player = $player;
	}
}