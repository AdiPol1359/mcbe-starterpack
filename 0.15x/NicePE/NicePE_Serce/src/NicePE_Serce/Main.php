<?php

namespace NicePE_Serce;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

class Main extends PluginBase implements Listener{
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info("Plugin włączono");
	}
	public function onDisable(){
		$this->getLogger()->info("Plugin wyłączono");
	}
	  public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
	 if($cmd->getName()=="serce"){
	 	if(!$sender->hasPermission("nicepe.serce")){
	 	$sender->sendMessage("§8• [§cNicePE§8] §7Aby uzyc tej komendy musisz miec range SWAGGER §8•");
	 	return false;
	 	}
	 	if($sender->hasPermission("nicepe.serce")){
	 	$sender->sendMessage("§8• [§cNicePE§8] §7Otrzymales §c1 §7dodatkowe serce §8•");
	 	$sender->setMaxHealth(22);
	  $sender->setHealth(22);
	 }
	 }
	 }
	 public function onDeath(PlayerDeathEvent $e){
	 	$gracz = $e->getPlayer();
	 	$gracz->setMaxHealth(20); 
	 }
	}