<?php
namespace EssentialsPE\Tasks\AFK;

use EssentialsPE\BaseFiles\BaseTask;
use EssentialsPE\Loader;
use pocketmine\utils\TextFormat;

class AFKSetterTask extends BaseTask{

    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin);
    }

    /*
     * This task is executed every 30 seconds,
     * with the purpose of checking all players' last movement
     * time, stored in their 'Session',
     * and check if it is pretty near,
     * or it's over, the default Idling limit.
     *
     * If so, they will be set in AFK mode
     */

    /**
     * @param int $currentTick
     */
    public function onRun($currentTick){
        $this->getPlugin()->getServer()->getLogger()->debug(TextFormat::YELLOW . "Running EssentialsPE's AFKSetterTask");
        foreach($this->getPlugin()->getServer()->getOnlinePlayers() as $p){
            if(!$this->getPlugin()->isAFK($p) && ($last = $this->getPlugin()->getLastPlayerMovement($p)) !== null && !$p->hasPermission("essentials.afk.preventauto")){
                if(time() - $last >= $this->getPlugin()->getConfig()->getNested("afk.auto-set")){
                    $this->getPlugin()->setAFKMode($p, true, $this->getPlugin()->getConfig()->getNested("afk.auto-broadcast"));
                }
            }
        }
        // Re-Schedule the task xD
        $this->getPlugin()->scheduleAutoAFKSetter();
    }
}