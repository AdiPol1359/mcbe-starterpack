<?php

namespace Core\commands;

use Core\task\HomeTask;
use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use Core\Main;
use Core\api\HomeAPI;

class HomeCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("home", "Komenda home");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

	    if(empty($args)) {
	        $homes = HomeAPI::getHomes($sender);

	        if(empty($homes))
	            $sender->sendMessage(Main::format("Nie posiadasz zadnych domow!"));
	        else
	            $sender->sendMessage(Main::format("Twoje domy: §4".implode("§8, §4", $homes)));
	        return;
        }

	    if(!HomeAPI::isHomeExists($sender, $args[0])) {
	        $sender->sendMessage(Main::format("Ten dom nie istnieje!"));
	        return;
        }

        $time = Main::getInstance()->getTeleportTime($sender);

        $sender->sendMessage(Main::format("Teleportacja nastapi za §4$time §7sekund, nie ruszaj sie!"));

        $sender->addEffect(new EffectInstance(Effect::getEffect(9), 20*$time, 3));

        if(isset(Main::$homeTask[$sender->getName()]))
            Main::$homeTask[$sender->getName()]->cancel();

        Main::$homeTask[$sender->getName()] = Main::getInstance()->getScheduler()->scheduleDelayedTask(new HomeTask($sender, HomeAPI::getHomePos($sender, $args[0]), $args[0]), 20*$time);
	}
}