<?php

namespace PolishMC_Allert;

use pocketmine\plugin\PluginBase;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

class Main extends PluginBase{
	
	public function onEnable(){
		$this->getLogger()->info("Plugin włączono");
	}
	public function onDisable(){
		$this->getLogger()->info("Plugin wyłączono");
	}
	  public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args) : bool {
	 if($cmd->getName() == "allert"){
	 if($sender->hasPermission("allert.command")){
	 		$wiadomosc = trim(implode(" ", $args));
	 		foreach($this->getServer()->getOnlinePlayers() as $p){
	 			$p->addTitle("§l§eGrayHC", "§l§7$wiadomosc", 0, 20*2, 0);
	 		}
	 	}
	 }
	 return false;
	 }
	}
