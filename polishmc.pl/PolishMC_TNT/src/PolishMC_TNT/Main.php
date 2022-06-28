<?php

namespace PolishMC_TNT;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\utils\Config;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use pocketmine\event\block\BlockPlaceEvent;

class Main extends PluginBase implements Listener{
	
	public function format($wiadomosc){
		return "§8• [§cPOLISHMC§8] §7$wiadomosc §8•";
	}
	
	public function onEnable(){
		
		$this->tnt = new Config($this->getDataFolder(). "TNT.yml", Config::YAML);
		
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info("Plugin włączono");
	}
	public function onDisable(){
		$this->getLogger()->info("Plugin wyłączono");
	}
	  public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args) : bool{
	 if($cmd->getName() == "tnt"){
	 	if($sender->hasPermission("tnt.command")){
	 		if(empty($args)){
	 	$sender->sendMessage($this->format("Uzyj /tnt (on/off)"));
	 	}
	 	if(isset($args[0])){
	 		if($args[0] == "on"){
	 			$this->tnt->set("tnt", "on");
	 			$this->tnt->save();
	 			$this->getServer()->broadcastMessage($this->format("Stawianie §cTNT §7na serwerze zostalo §awlaczone"));
	 		}
	 		
	 		if($args[0] == "off"){
	 			$this->tnt->set("tnt", "off");
	 			$this->tnt->save();
	 			$this->getServer()->broadcastMessage($this->format("Stawianie §cTNT §7na serwerze zostalo §cwylaczone"));
	 		}
	 	}
	 	}else{
	 		$sender->sendMessage($this->f("Nie posiadasz permisji §8(§ctnt.command§8)"));
	 	}
	 }
	 
	 return false;
	  }
	  
	  public function TNT(BlockPlaceEvent $e){
	  	$gracz = $e->getPlayer();
	  	$blok = $e->getBlock();
	  	
	  	if($blok->getId() == 46 && $this->tnt->get("tnt") == "off"){
	  		$e->setCancelled(true);
	  		$gracz->sendMessage($this->format("Stawianie §cTNT §7na serwerze jest §cwylaczone"));
	  	}
	  }
	}