<?php
namespace EssentialsPE\Events;

use EssentialsPE\Loader;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class SessionCreateEvent extends PluginEvent{
    public static $handlerList = null;

    /** @var Loader  */
    public $plugin;
    /** @var Player  */
    public $player;
    /** @var array  */
    public $values;

    /**
     * @param Loader $plugin
     * @param Player $player
     * @param array $values
     */
    public function __construct(Loader $plugin, Player $player, array $values){
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $this->player = $player;
        $this->values = $values;
    }

    /**
     * Return all the Session Values
     *
     * @return array
     */
    public function getValues(){
        return $this->values;
    }

    /**
     * Replace a specific Session Value
     *
     * @param $key
     * @param $value
     */
    public function setValue($key, $value){
        if(!isset($this->values[$key])){
            return;
        }
        $this->values[$key] = $value;
    }

    /**
     * Set the Session Values
     *
     * @param array $values
     */
    public function setValues(array $values){
        $this->values = $values;
    }

    /**
     * return the Player to work on
     *
     * @return Player
     */
    public function getPlayer(){
        return $this->player;
    }
} 