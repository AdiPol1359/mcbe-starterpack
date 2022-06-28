<?php

namespace Core\task;

use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Server;
use pocketmine\scheduler\Task;
use Core\api\LobbyAPI;
use Core\bossbar\{
	Bossbar, BossbarManager
};
use Core\Main;

class LobbyTask extends Task {
	
	private $bossbar;

	private $lastSound = 0;
	private $lastMessage = 0;
	
	public function __construct() {
		$this->bossbar = new Bossbar("");
	}

	public function onRun(int $currentTick) : void {
		$server = Server::getInstance();
		
		if(LobbyAPI::isLobbyEnabled()) {
			foreach($server->getOnlinePlayers() as $player)
			 if(BossbarManager::getBossbar($player) == null)
			  $this->bossbar->showTo($player);

			 if($this->lastSound == 3) {
			    $level = $server->getLevelByName("lobby");
			    $level->broadcastLevelSoundEvent($level->getSafeSpawn(), LevelSoundEventPacket::SOUND_PORTAL);
			    $this->lastSound = 0;
			}
			 $this->lastSound++;

			 if($this->lastMessage == LobbyAPI::getMessageEvery()) {
			     $this->lastMessage = 0;
			     $message = LobbyAPI::getMessage();

			     if($message != null) {
			         if(is_array($message))
			             $message = Main::formatLines($message);
			         else
			             $message = Main::format($message);

                     $level = $server->getLevelByName("lobby");
                     foreach($level->getPlayers() as $player)
                         $player->sendMessage($message);
                 }
             }
			 $this->lastMessage++;

			$this->bossbar->setTitle(LobbyAPI::dateFormat());
			if(LobbyAPI::getLobbyDate() != null && time() >= strtotime(LobbyAPI::getLobbyDate())) {
				LobbyAPI::setLobby(false);
				
				$this->bossbar->hideFromAll();
				
				foreach($server->getLevelByName("lobby")->getPlayers() as $levelPlayer) {
					$levelPlayer->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
					foreach($server->getOnlinePlayers() as $serverPlayer) {
						if($levelPlayer === $serverPlayer)
						 continue;
						
						$levelPlayer->showPlayer($serverPlayer);
						$serverPlayer->showPlayer($levelPlayer);
					}
				}
			}
		}
	}
}