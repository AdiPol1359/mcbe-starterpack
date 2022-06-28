<?php

declare(strict_types=1);

namespace permissionex\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use permissionex\Main;
use permissionex\managers\{
	ChatManager, FormatManager
};

class ChatListener implements Listener {

    /**
     * @param PlayerChatEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
	public function chatFormat(PlayerChatEvent $e) {
		$player = $e->getPlayer();
	 $groupManager = Main::getInstance()->getGroupManager();
	 
	 $group = $groupManager->getPlayer($player->getName())->getGroup();
	 
	 if($group != null && $group->getFormat() != null) {
	 	$format = FormatManager::getFormat($player, $group->getFormat(), $e->getMessage());
	 	
	 	if(!ChatManager::isChatPerWorld())
	   $e->setFormat($format);
	  else {
	  	$e->setCancelled(true);
	  	
	  	foreach($player->getLevel()->getPlayers() as $p)
	  	 $p->sendMessage($format);
	  }
	 }
	}
}