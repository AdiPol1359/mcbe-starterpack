<?php

namespace kozak;

use pocketmine\plugin\PluginBase as PluginBase;
use pocketmine\event\Listener as Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\block\Air;
use pocketmine\block\Stone;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\level\particle\LavaParticle;
use pocketmine\level\sound\BlazeShootSound;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
class Main extends PluginBase implements Listener{

 
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);		
		$this->saveDefaultConfig();
		$this->getServer()->getLogger()->info(TextFormat::GREEN . "kozak");
	}
	 public function onPlace(BlockPlaceEvent $event){
		 $player = $event->getPlayer();
		 $block = $event->getBlock();
		 $x = $block->getX();
		 $y = $block->getY();
		 $z = $block->getZ();
		 if($block->getId() == 121){
       $player->sendPopup("§bPostawiles stoniarke");
		 if($player->hasPermission("stoniarka.sponsor")){
		 if($player->getLevel()->getBlock(new Vector3($x, $y+1, $z))->getId() == 0){
	     $player->getLevel()->setBlock(new Vector3($x, $y+1, $z), new Stone());
     if($player->getLevel()->getBlock(new Vector3($x, $y+2, $z))->getId() == 0){
	     $player->getLevel()->setBlock(new Vector3($x, $y+2, $z), new Stone());
		 }
		 }
		 }
	 }
}
	 public function onBreak(BlockBreakEvent $event){
	  $blok = $event->getBlock();
	  $gracz = $event->getPlayer();
	  $y = $blok->getFloorY();
	  $x = $blok->getFloorX();
  	 $z = $blok->getFloorZ();
  	  if($blok->getId() == 1){
	// gora -1
  	   if($gracz->getLevel()->getBlock(new Vector3($x, $y-1, $z))->getId() == 121) {	   
	   $task = new Task($this, $event->getBlock()->getFloorX(), $event->getBlock()->getFloorY(), $event->getBlock()->getFloorZ());
       if($gracz->hasPermission("stoniarka.gracz")){
       $this->getServer()->getScheduler()->scheduleDelayedTask($task, 40);
	   }
	   if($gracz->hasPermission("stoniarka.vip")){
       $this->getServer()->getScheduler()->scheduleDelayedTask($task, 35);
	   }
	   if($gracz->hasPermission("stoniarka.sponsor")){
       $this->getServer()->getScheduler()->scheduleDelayedTask($task, 30);
	   }
	   $drops = array(Item::get(0, 0, 0));
      $event->setDrops($drops);
  	      }
    // gora -2
	if($gracz->getLevel()->getBlock(new Vector3($x, $y-2, $z))->getId() == 121) {	   
	   $task = new Task2($this, $event->getBlock()->getFloorX(), $event->getBlock()->getFloorY(), $event->getBlock()->getFloorZ());
	   if($gracz->hasPermission("stoniarka.gracz")){
       $this->getServer()->getScheduler()->scheduleDelayedTask($task, 40);
	   }
	   if($gracz->hasPermission("stoniarka.vip")){
       $this->getServer()->getScheduler()->scheduleDelayedTask($task, 30);
	   }
	   if($gracz->hasPermission("stoniarka.sponsor")){
       $this->getServer()->getScheduler()->scheduleDelayedTask($task, 20);
	   }
	   $drops = array(Item::get(0, 0, 0));
      $event->setDrops($drops);
  	   }
	 }
}

}
