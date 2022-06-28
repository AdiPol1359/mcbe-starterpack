<?php
namespace EssentialsPE\Events;

use EssentialsPE\Loader;
use pocketmine\event\Cancellable;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class PlayerFlyModeChangeEvent extends PluginEvent implements Cancellable{
    public static $handlerList = null;

    /** @var Player */
    protected $player;
    /** @var bool */
    protected $isFlying;
    /** @var bool */
    protected $mode;

    /**
     * @param Loader $plugin
     * @param Player $player
     * @param bool $mode
     */
    public function __construct(Loader $plugin, Player $player, $mode){
        parent::__construct($plugin);
        $this->player = $player;
        $this->isFlying = $plugin->canFly($player);
        $this->mode = $mode;
    }

    /**
     * The player to work over
     *
     * @return Player
     */
    public function getPlayer(){
        return $this->player;
    }

    /**
     * The current "flying" status of the player
     *
     * @return bool
     */
    public function getCanFly(){
        return $this->isFlying;
    }

    /**
     * The "flying" status to set
     *
     * @return bool
     */
    public function willFly(){
        return $this->mode;
    }

    /**
     * Modify the "flying" status to be set
     *
     * @param bool $mode
     */
    public function setCanFly($mode){
        $this->mode = $mode;
    }
}