<?php

declare(strict_types=1);

namespace permissionex\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use permissionex\Main;
use permissionex\managers\NameTagManager;

class JoinListener implements Listener {
	
	public function permissionsOnJoin(PlayerJoinEvent $e) {
		$player = $e->getPlayer();
	 $groupManager = Main::getInstance()->getGroupManager();
	 $groupManager->registerPlayer($player);
	 
	 if(!$groupManager->getPlayer($player->getName())->hasGroup()) {
	 	 if($groupManager->getDefaultGroup() == null) {
	 	 	$player->sendMessage(Main::format("Default group not found!"));
	 	 	return;
	 	 }
	  $groupManager->getPlayer($player->getName())->addDefaultGroup();
	 }
	}
	
	public function updateNametag(PlayerJoinEvent $e) {
		NameTagManager::updateNameTag($e->getPlayer());
	}
}