<?php

namespace PolishMC_Sprawdzanie;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\math\Vector3;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use pocketmine\event\player\PlayerCommandPreprocessEvent;

use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
	
	public function f($w){
		return "§8• [§cPOLISHMC§8] §7$w §8•";
	}
	
	public function onEnable(){
		
		$this->kordy = new Config($this->getDataFolder() . "Kordy.yml", CONFIG::YAML);
		
		if(!($this->kordy->exists("Kordy"))){
			$this->kordy->set("Kordy", array(
			"x" => 100,
			"y" => 100,
			"z" => 100
			));
			$this->kordy->save();
		}
		
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info("Plugin włączono");
	}
	public function onDisable(){
		$this->getLogger()->info("Plugin wyłączono");
	}
	  public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args) : bool{
	 if($cmd->getName() == "sprawdzanie"){
	 	if($sender->hasPermission("sprawdzanie.command")){
	 		if(empty($args)){
	 			$sender->sendMessage("§7Komendy:");
	 			$sender->sendMessage("§8- §7/sprawdzanie ustaw");
	 			$sender->sendMessage("§8- §7/sprawdzanie sprawdz (nick)");
	 			$sender->sendMessage("§8- §7/sprawdzanie zbanuj (nick)");
	 			$sender->sendMessage("§8- §7/sprawdzanie czysty (nick)");
	 		}
	 		if(isset($args[0])){
	 			if($args[0] == "ustaw"){
	 				$x = $sender->getX();
	 				$y = $sender->getY();
	 				$z = $sender->getZ();
	 				
	 				$this->kordy->set("Kordy", array(
			"x" => $x,
			"y" => $y,
			"z" => $z
			));
			$this->kordy->save();
			$sender->sendMessage($this->f("Kordy sprawdzarki zostaly ustawione"));
	 			}
	 			if($args[0] == "sprawdz"){
	 				if(!(isset($args[1]))){
	 					$sender->sendMessage($this->f("Uzyj /sprawdzanie sprawdz (nick)"));
	 					return true;
	 				}
	 				if(isset($args[1])){
	 					$nick = $args[1];
	 					$gracz = $this->getServer()->getPlayer($nick);
	 					if(!($gracz)){
	 						$sender->sendMessage($this->f("Ten gracz jest offline"));
	 						return true;
	 					}
	 					
	 					$nick = $gracz->getName();
	 					
	 					if(isset($this->sprawdzanie[$nick])){
	 						$sender->sendMessage($this->f("Ten gracz jest juz sprawdzany"));
	 						return true;
	 					}
	 						 					
	 					$this->sprawdzanie[$nick] = true;
	 					
	 					$this->getServer()->broadcastMessage($this->f("Gracz §c$nick §7zostal wezwany do sprawdzania przez administratora §c{$sender->getName()}"));
	 					
	 					$gracz->sendMessage($this->f("Jestes sprawdzany!"));
	 					$gracz->sendMessage($this->f("Mozesz uzywac tylko komend §c/msg §7i §c/r"));
	 					
	 			$array = $this->kordy->getNested("Kordy");
	 			$x = $array["x"];
	 			$y = $array["y"];
	 			$z = $array["z"];
	 			
	 			$sender->teleport(new Vector3($x, $y, $z));
	 			$gracz->teleport(new Vector3($x, $y, $z));
	 				}
	 			}
	 			
	 			if($args[0] == "czysty"){
	 				if(!(isset($args[1]))){
	 					$sender->sendMessage($this->f("Uzyj /sprawdzanie czysty (nick)"));
	 					return true;
	 				}
	 				if(isset($args[1])){
	 					$nick = $args[1];
	 					$gracz = $this->getServer()->getPlayer($nick);
	 					if(!($gracz)){
	 						$sender->sendMessage($this->f("Ten gracz jest offline"));
	 						return true;
	 					}
	 					
	 					$nick = $gracz->getName();
	 					
	 					if(!(isset($this->sprawdzanie[$nick]))){
	 						$sender->sendMessage($this->f("Ten gracz nie jest sprawdzany"));
	 						return true;
	 					}
	 					
	 					unset($this->sprawdzanie[$nick]);
	 					
	 					$this->getServer()->broadcastMessage($this->f("Gracz §c$nick §7okazal sie byc czysty"));
	 				
	 			$gracz->teleport($gracz->getLevel()->getSafeSpawn());
	 				}
	 			}
	 			
	 			if($args[0] == "zbanuj"){
	 				if(!(isset($args[1]))){
	 					$sender->sendMessage($this->f("Uzyj /sprawdzanie zbanuj (nick)"));
	 					return true;
	 				}
	 				if(isset($args[1])){
	 					$nick = $args[1];
	 					$gracz = $this->getServer()->getPlayer($nick);
	 					if(!($gracz)){
	 						$sender->sendMessage($this->f("Ten gracz jest offline"));
	 						return true;
	 					}
	 					
	 					$nick = $gracz->getName();
	 					
	 					if(!(isset($this->sprawdzanie[$nick]))){
	 						$sender->sendMessage($this->f("Ten gracz nie jest sprawdzany"));
	 						return true;
	 					}
	 					
	 					unset($this->sprawdzanie[$nick]);
	 					
	 					$this->getServer()->broadcastMessage($this->f("Gracz §c$nick §7zostal zbanowany z powodu §ccheatow"));
	 				
	 			$gracz->close("§cYou are Banned", true);
	 				}
	 			}
	 		}
	 }else{
	 	$sender->sendMessage($this->f("Nie posiadasz permisji §8(§csprawdzanie.command§8)"));
	 }
	  }
	  return false;
	 }
	 
	 public function BlokadaKomend(PlayerCommandPreprocessEvent $e){
	 	$gracz = $e->getPlayer();
	 	$nick = $gracz->getName();
	 	$komenda = $e->getMessage();
	 	
	 	if(isset($this->sprawdzanie[$nick])){
	 	if($komenda[0] == "/"){
	 		if(!($komenda == "/msg" || $komenda == "/r")){
	 			$e->setCancelled(true);
	 			$gracz->sendMessage($this->f("Jestes sprawdzany, nie mozesz uzyc tej komendy"));
	 		}
	 	}
	 }
	}
}