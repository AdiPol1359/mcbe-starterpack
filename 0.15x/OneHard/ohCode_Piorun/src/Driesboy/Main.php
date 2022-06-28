<?php
#   _____                       _____            __ _     _____  ______ 
#  / ____|                     / ____|          / _| |   |  __ \|  ____|
# | |  __  __ _ _ __ ___   ___| |     _ __ __ _| |_| |_  | |__) | |__   
# | | |_ |/ _` | '_ ` _ \ / _ \ |    | '__/ _` |  _| __| |  ___/|  __|  
# | |__| | (_| | | | | | |  __/ |____| | | (_| | | | |_  | |    | |____ 
#  \_____|\__,_|_| |_| |_|\___|\_____|_|  \__,_|_|  \__| |_|    |______|
                                                                       
                                                                       
namespace Driesboy;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\SetTimePacket;
use pocketmine\network\protocol\TextPacket;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\entity\Entity;

class Main extends PluginBase implements Listener{
	/** @var AddEntityPacket */
	private $lightning;
	
	public function onEnable() {
                $this->getLogger()->info("§6Lightning by Driesboy is Enabled");
                $this->getServer()->getPluginManager()->registerEvents($this, $this);
                $this->saveResource("Config.yml");
	}
        public function onDisable() {
	        $this->getlogger()->info("§6Lightning by Driesboy is Disable");	
	}	
	
	/**
	 * @param PlayerDeathEvent $e
	 * @ignoreCancelled true
	 * 
	 * @priority MONITOR
	 */
   public function onDeath(PlayerDeathEvent $e){
   	if ($this->getConfig()->get("Death") === true) {
	$p = $e->getEntity();
        $level = $p->getLevel();
	$light = new AddEntityPacket();
        $light->type = 93;
        $light->eid = Entity::$entityCount++;
        $light->metadata = array();
        $light->speedX = 0;
        $light->speedY = 0;
        $light->speedZ = 0;
        $light->yaw = $p->getYaw();
        $light->pitch = $p->getPitch();
        $light->x = $p->x;
        $light->y = $p->y;
        $light->z = $p->z;
        foreach($level->getPlayers() as $pl){
            $pl->dataPacket($light);
        } 
     }
   } 
}
