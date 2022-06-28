<?php

namespace NicePE_Kordy;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
	
	public function onEnable(){
		@mkdir($this->getDataFolder());
 $this->kordy = new Config($this->getDataFolder() . "KordyOnOff.yml", Config::YAML);
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info("Plugin włączono");
	}
	public function onDisable(){
		$this->getLogger()->info("Plugin wyłączono");
	}
		public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
       	if($cmd->getName() == "kordy"){
				if(!isset($args[0])){
					$sender->sendMessage("§8• [§cNicePE§8] §7Użyj /kordy (on/off) §8•");
					}
					if($args[0] == "on"){
						$this->kordy->set($sender->getName());
						$this->kordy->save();
						$sender->sendMessage("§8• [§cNicePE§8] §7Właczyłes pokazywanie kordów! §8•");
					}
					if($args[0] == "off"){
						$this->kordy->remove($sender->getName());
						$this->kordy->save();
						$sender->sendMessage("§8• [§cNicePE§8] §7Wyłaczyłes pokazywanie kordów! §8•");
					}
			}
	}
	 public function kordy(PlayerMoveEvent $e){
	 	$gracz = $e->getPlayer();
	 	$nick = $gracz->getName();
	 	if($this->kordy->get($nick)){
	 	$x = $gracz->getFloorX();
	 	$y = $gracz->getFloorY();
	 	$z = $gracz->getFloorZ();
	 	$gracz->sendPopup("§cX:§7 $x §cY:§7 $y §cZ:§7 $z");
	 }
	 }
	}