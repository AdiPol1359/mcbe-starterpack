<?php

namespace NicePE_FirstJoin;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

class Main extends PluginBase implements Listener{
	
	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->death = new Config($this->getDataFolder() . "Death.yml", Config::YAML);
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info("Plugin włączono");
	}
	public function onDisable(){
		$this->getLogger()->info("Plugin wyłączono");
	}
public function onJoin(PlayerJoinEvent $e){
	$gracz = $e->getPlayer();
	$nick = $gracz->getName();
	$x = mt_rand(1,1490);
	$z = mt_rand(1,1490);
	if($this->death->get($nick)){
  $gracz->teleport($gracz->getLevel()->getSafeSpawn());
  $this->death->remove($nick);
  $this->death->save();
	}
	if($gracz->hasPlayedBefore() == false){
	$gracz->teleport(new Vector3($x, 67, $z));
	$gracz->getInventory()->addItem(Item::get(1, 0, 64));
	}
	 }
	 /*
public function onDeath(PlayerDeathEvent $e){
	$gracz = $e->getPlayer();
	$nick = $gracz->getName();
	$powod = "§cZdedales!
 
§cWejdz ponownie na serwer!";
 $gracz->getInventory()->clearAll();
	$gracz->kick($powod, false);
	 $this->death->set($nick, "*");
  $this->death->save();
}*/
	}