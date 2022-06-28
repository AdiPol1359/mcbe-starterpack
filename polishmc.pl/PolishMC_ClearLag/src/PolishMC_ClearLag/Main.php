<?php

namespace PolishMC_ClearLag;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\scheduler\Task;

use pocketmine\entity\DroppedItem;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Creature;

use pocketmine\Player;
use pocketmine\Server;

class Main extends PluginBase implements Listener{
	
	protected $exemptedEntities = [];
	
 public function f(String $w){
		return "§8• [§cPOLISHMC§8] §7$w §8•";
	}
 
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getLogger()->info("Plugin włączono");
		
		$this->getScheduler()->scheduleRepeatingTask(new ClearLagTask($this), 20*120);
	}
	
	public function removeEntities(){
    $i = 0;
    foreach($this->getServer()->getLevels() as $level){
     foreach($level->getEntities() as $entity){
        if(!$this->isEntityExempted($entity) && !($entity instanceof Creature)) {
          $entity->close();
          $i++;
        }
      }
    }
    return $i;
  }
  
 public function exemptEntity(Entity $entity) {
    $this->exemptedEntities[$entity->getID()] = $entity;
  }

  public function isEntityExempted(Entity $entity) {
    return isset($this->exemptedEntities[$entity->getID()]);
  }
	
	public function onDisable(){
		$this->getLogger()->info("Plugin wyłączono");
	}
	}
	
class ClearLagTask extends Task{
	

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function onRun($tick){
    	$ilosc = $this->plugin->removeEntities();
    	
    	$this->plugin->getServer()->broadcastMessage($this->plugin->f("Usunieto §c$ilosc §7itemow ze swiata"));
  }
}