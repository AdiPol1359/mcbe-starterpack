<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

use Core\Main;

use Core\task\SpawnTask;

class SpawnCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("spawn", "Komenda spawn");
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;
		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}
		
		if($sender->hasPermission("vahc.spawn.ignoretime")) {
			$sender->teleport($sender->getLevel()->getSafeSpawn());
			$sender->sendMessage(Main::format("Przeteleportowano na spawna!"));
			return;
		}
		
		$nick = $sender->getName();

		$time = Main::getInstance()->getTeleportTime($sender);

		$sender->sendMessage(Main::format("Teleportacja nastapi za §4$time §7sekund, nie ruszaj sie!"));
		
		$sender->addEffect(new EffectInstance(Effect::getEffect(9), 20*$time, 3));

        if(isset(Main::$spawnTask[$nick]))
            Main::$spawnTask[$nick]->cancel();

        Main::$spawnTask[$nick] = Main::getInstance()->getScheduler()->scheduleDelayedTask(new SpawnTask($sender), 20*$time);
			
		
	}
}