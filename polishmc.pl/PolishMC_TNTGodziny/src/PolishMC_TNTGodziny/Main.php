<?php

namespace PolishMC_TNTGodziny;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\event\block\BlockPlaceEvent;

class Main extends PluginBase implements Listener{
	
	public function f($w){
		return "§8• [§cPOLISHMC§8] §7$w §8•";
	}
	
	public function onEnable(){
		
		date_default_timezone_set('Europe/Warsaw');
		
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info("Plugin włączono");
	}
	public function onDisable(){
		$this->getLogger()->info("Plugin wyłączono");
	}
	
	public function TNTOff(BlockPlaceEvent $e){
		$gracz = $e->getPlayer();
		$blok = $e->getBlock();
		$godzina = date("G");
		
			if($blok->getId() == 46){
				if(!($godzina >= 12 && $godzina <= 22)){
			$e->setCancelled(true);
			$gracz->sendMessage($this->f("TNT na serwerze jest wlaczone od godziny §c12 §7do godziny §c22"));
	 	}
 	}
 }
}