<?php

declare(strict_types=1);

namespace permissionex\listeners;

use pocketmine\event\Listener;
use permissionex\events\player\PlayerUpdateGroupEvent;
use permissionex\Main;
use permissionex\managers\NameTagManager;

class UpdateGroupListener implements Listener {
	
	public function updateNametag(PlayerUpdateGroupEvent $e) {
		NameTagManager::updateNameTag($e->getPlayer());
	}
}