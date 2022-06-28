<?php

namespace obsydianiarka;

use pocketmine\plugin\PluginBase as PluginBase;
use pocketmine\event\Listener as Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\block\Air;
use pocketmine\block\Obsidian;
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
		$this->getServer()->getLogger()->info(TextFormat::GREEN . "ble ble");
	}
	
	public function onPlace(BlockPlaceEvent $event){
	 $blok = $event->getBlock();
	 $gracz = $event->getPlayer();
	 $y = $blok->getFloorY();
	 $x = $blok->getFloorX();
	 $z = $blok->getFloorZ();
  	 if($blok->getId() == 121){
  	  if(!($event->isCancelled())){
        $center = new Vector3($x, $y, $z);
        for($yaw = 0, $y = $center->y; $y < $center->y + 3; $yaw += (M_PI * 2) / 20, $y += 1 / 20) {
            $x = -sin($yaw) + $center->x;
            $z = cos($yaw) + $center->z;
    }
	  }	 
	 }
 }
 
 public function onPlacewad(BlockPlaceEvent $event){
	 $blok = $event->getBlock();
	 $gracz = $event->getPlayer();
  	 if($blok->getId() == 121){
		 $event->getPlayer()->sendMessage("§7Postawiles §estoniarke§7! §8•");
		 $event->getPlayer()->sendMessage("§7Postaw na niej §estone§7! §8•");
	 }
 }
	 
	 public function onBreak(BlockBreakEvent $event){
	  $blok = $event->getBlock();
	  $gracz = $event->getPlayer();
	  $y = $blok->getFloorY();
	  $x = $blok->getFloorX();
  	 $z = $blok->getFloorZ();
  	  if($blok->getId() == 1){
  	   if($gracz->getLevel()->getBlock(new Vector3($x, $y-1, $z))->getId() == 121) {		   
  	    $gracz->getInventory()->addItem(Item::get(1, 0, 1));
		$gracz->getInventory()->addItem(Item::get(4, 0, 1));
	   $task = new Task($this, $event->getBlock()->getFloorX(), $event->getBlock()->getFloorY(), $event->getBlock()->getFloorZ());
       $this->getServer()->getScheduler()->scheduleDelayedTask($task, 20);
	   $drops = array(Item::get(0, 0, 0));
      $event->setDrops($drops);
  	      }
  	   }
	 }
}