<?php
namespace EssentialsPE\Events;

use EssentialsPE\Loader;
use pocketmine\event\Cancellable;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class PlayerVanishEvent extends PluginEvent implements Cancellable{
    public static $handlerList = null;

    /** @var Player  */
    protected $player;
    /** @var bool  */
    protected $isVanished;
    /** @var bool */
    protected $willVanish;
    /** @var bool */
    protected $noPacket;
    /** @var array */
    protected $keepHiddenFor = [];

    /**
     * @param Loader $plugin
     * @param Player $player
     * @param bool $willVanish
     * @param bool $noPacket
     */
    public function __construct(Loader $plugin, Player $player, $willVanish, $noPacket){
        parent::__construct($plugin);
        $this->player = $player;
        $this->isVanished = $plugin->isVanished($player);
        $this->willVanish = $willVanish;
        $this->noPacket = $noPacket;
    }

    /**
     * Return the player that will be vanished/shown
     *
     * @return Player
     */
    public function getPlayer(){
        return $this->player;
    }

    /**
     * Tell if the player is already vanished or not
     *
     * @return bool
     */
    public function isVanished(){
        return $this->isVanished;
    }

    /**
     * Tell if the player will be vanished or showed
     * false = Player will be showed
     * true = Player will be vanished
     *
     * @return bool
     */
    public function willVanish(){
        return $this->willVanish;
    }

    /**
     * Change the vanish mode that will be set
     * false = Player will be shown
     * true = Player will be vanished
     *
     * @param bool $value
     */
    public function setVanished($value){
        if(is_bool($value)){
            $this->willVanish = $value;
        }
    }

    /**
     * Tell if you prefer to use (or not) Player Packets instead of Effect ones.
     * false = Use Effect Packets
     * true = Use Player Packets
     *
     * @return bool
     */
    public function noPacket(){
        return $this->noPacket;
    }

    /**
     * Change the Packets to be used in the event.
     * false = Use Effect Packets
     * true = Use Player Packets
     *
     * @param bool $state
     */
    public function setNoPacket($state){
        if(is_bool($state)){
            $this->noPacket = $state;
        }
    }

    /**
     * This method will allow you to keep a player
     * hidden to other players, but EssentialsPE
     * will no longer consider it has "Vanished"
     *
     * @param Player $player
     */
    public function keepHiddenFor(Player $player){
        $this->keepHiddenFor[] = $player->getName();
    }

    /**
     * Return a list with all the players that
     * will not see the "unVanished" player
     *
     * @return array
     */
    public function getHiddenFor(){
        return $this->keepHiddenFor;
    }
} 