<?php

namespace xSmoothy;

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
		$this->getServer()->getLogger()->info(TextFormat::GREEN . "Wszelkie błędy zgłaszaj do xSmoothy'iego");
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
	  }else{
       $gracz->sendMessage(TextFormat::RED . "[HC] " . TextFormat::WHITE . "Ten teren jest zabezpieczony!");
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
  	   if($gracz->getLevel()->getBlock(new Vector3($x, $y-1, $z))->getId() == 121) {
		$gracz->getInventory()->addItem(Item::get(4, 0, 1));
	   $task = new Task($this, $event->getBlock()->getFloorX(), $event->getBlock()->getFloorY(), $event->getBlock()->getFloorZ());
       $this->getServer()->getScheduler()->scheduleDelayedTask($task, 30);
	   $drops = array(Item::get(0, 0, 0));
      $event->setDrops($drops);
  	      }
  	   }
	 }
}