<?php

namespace PolishMC_Scale;

use pocketmine\plugin\PluginBase;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

class Main extends PluginBase{
	
	public function f(String $w){
		return "§8• [§cPOLISHMC§8] §7$w §8•";
	}
	
	public function onEnable(){
		$this->getLogger()->info("Plugin włączono");
	}
	public function onDisable(){
		$this->getLogger()->info("Plugin wyłączono");
	}
	  public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args) : bool{
	 if($cmd->getName() == "scale"){
	 	if($sender->hasPermission("scale.command")){
	 		if(empty($args)){
	 			$sender->sendMessage($this->f("Uzyj:"));
	 			$sender->sendMessage($this->f("/scale (liczba)"));
	 			$sender->sendMessage($this->f("/scale resetuj"));
	 		}
	 		if(isset($args[0])){
	 			if($args[0] == "resetuj"){
	 				$sender->sendMessage($this->f("Twoje scalowanie zostalo §czresetowane"));
	 				$sender->setScale(1);
	 				return true;
	 			}
	 		 
	 		 $sender->sendMessage($this->f("Twoje scalowanie zostalo ustawione na §c$args[0]"));
	 				$sender->setScale($args[0]);
	 		}
	 }else{
	 	$sender->sendMessage($this->f("Nie posiadasz permisji §8(§cscale.command§8)"));
	 }
	 }
	 return false;
	 }
	}