<?php

declare(strict_types=1);

namespace Core\level\generator;

use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\level\generator\GeneratorManager as GManager;

class GeneratorManager {
	
	public static function init() : void {	//GManager::addGenerator(VoidGenerator::class, "void");
	//self::createVoidLevel("lobby");
	}
	
	public static function createVoidLevel(string $levelName) : void {
		$server = Server::getInstance();
		$server->broadcastMessage("OKKKKKKK");
		if($server->getLevelByName($levelName) == null) {
			$server->broadcastMessage("OKKKKKKK");
			$server->generateLevel($levelName, null, VoidGenerator::class);
			$server->loadLevel($levelName);
			$server->getLevelByName($levelName)->setSpawnLocation(new Vector3(1, 1, 1));
		} else
		 $server->loadLevel($levelName);
	}
}