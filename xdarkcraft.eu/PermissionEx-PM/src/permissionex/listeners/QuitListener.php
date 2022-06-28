<?php

declare(strict_types=1);

namespace permissionex\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use permissionex\Main;

class QuitListener implements Listener {
	
	public function unregisterPlayer(PlayerQuitEvent $e) {
		Main::getInstance()->getGroupManager()->unregisterPlayer($e->getPlayer());
	}
}