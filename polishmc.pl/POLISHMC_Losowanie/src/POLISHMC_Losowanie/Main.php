<?php

namespace POLISHMC_Losowanie;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;

use pocketmine\event\player\PlayerChatEvent;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

class Main extends PluginBase implements Listener{
	
	public $losowanie = [];
	
	public function f($w){
		return "§8• [§cPOLISHMC§8] §7$w §8•";
	}
	
	public function RandomWord($len){
    $word = array_merge(range('a', 'z'), range('A', 'Z'));
    shuffle($word);
    return substr(implode($word), 0, $len);
}
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info("Plugin włączono");
	}
	public function onDisable(){
		$this->getLogger()->info("Plugin wyłączono");
	}
	  public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args) : bool{
	 if($cmd->getName() == "losowanie"){
	 	if($sender->hasPermission("losowanie.command")){
	 		if(empty($args)){
	 	$sender->sendMessage($this->f("Uzyj /losowanie (vip, svip, sponsor)"));
	 	}
	 	if(isset($args[0])){
	 		if($args[0] == "vip"){
	 			foreach($this->losowanie as $ranga => $kod){
	 		if(isset($this->losowanie[$ranga])){
	 			$sender->sendMessage($this->f("Losowanie juz wystartowalo"));
	 			return true;
	 		}
	 		}
	 		
	 		$kod = $this->RandomWord(15);
	 		
	 		$this->losowanie["Vip"] = $kod;
	 		$this->getServer()->broadcastMessage($this->f("Kto 1 przepisze kod wygrywa range §cVIP"));
	 		$this->getServer()->broadcastMessage($this->f("Kod: $kod"));
	 		}
	 		
	 		if($args[0] == "svip"){
	 			foreach($this->losowanie as $ranga => $kod){
	 		if(isset($this->losowanie[$ranga])){
	 			$sender->sendMessage($this->f("Losowanie juz wystartowalo"));
	 			return true;
	 		}
	 		}
	 		
	 		$kod = $this->RandomWord(15);
	 		
	 		$this->losowanie["Svip"] = $kod;
	 		$this->getServer()->broadcastMessage($this->f("Kto 1 przepisze kod wygrywa range §cSVIP"));
	 		$this->getServer()->broadcastMessage($this->f("Kod: $kod"));
	 		}
	 		
	 		if($args[0] == "sponsor"){
	 			foreach($this->losowanie as $ranga => $kod){
	 		if(isset($this->losowanie[$ranga])){
	 			$sender->sendMessage($this->f("Losowanie juz wystartowalo"));
	 			return true;
	 		}
	 		}
	 		
	 		$kod = $this->RandomWord(15);
	 		
	 		$this->losowanie["Sponsor"] = $kod;
	 		$this->getServer()->broadcastMessage($this->f("Kto 1 przepisze kod wygrywa range §cSPONSOR"));
	 		$this->getServer()->broadcastMessage($this->f("Kod: $kod"));
	 		}
	 	}
	 }else{
	 	$sender->sendMessage($this->f("Nie posiadasz permisji §8(§closowanie.command§8)"));
	 }
	 }
	 return false;
	  }
	 
	 public function onChat(PlayerChatEvent $e){
	 	$gracz = $e->getPlayer();
	 	$nick = $gracz->getName();
	 	$wiadomosc = $e->getMessage();
	 	
	 	if(isset($this->losowanie)){
	 		
	 	foreach($this->losowanie as $ranga => $kod){
	 			if($wiadomosc == $kod){
	 				$e->setCancelled(true);
	 				unset($this->losowanie[$ranga]);
	 				$this->getServer()->broadcastMessage($this->f("Losowanie dobieglo konca"));
	 				$this->getServer()->broadcastMessage($this->f("Range §c$ranga §7wygral gracz §c$nick"));
	 				
	 				$this->getServer()->dispatchCommand(new ConsoleCommandSender(), 'setgroup $nick $ranga');
	 			}
	 			
	 		}
	 		
	 	}
	 }
	}