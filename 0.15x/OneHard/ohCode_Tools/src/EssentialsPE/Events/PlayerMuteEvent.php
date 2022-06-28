<?php
namespace EssentialsPE\Events;

use EssentialsPE\Loader;
use pocketmine\event\Cancellable;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class PlayerMuteEvent extends PluginEvent implements Cancellable{
    public static $handlerList = null;

    /** @var Player  */
    protected $player;
    /** @var  bool */
    protected $isMuted;
    /** @var  bool */
    protected $mode;
    /** @var \DateTime|null */
    protected $expires;

    /**
     * @param Loader $plugin
     * @param Player $player
     * @param bool $mode
     * @param \DateTime $expires
     */
    public function __construct(Loader $plugin, Player $player, $mode, \DateTime $expires = null){
        parent::__construct($plugin);
        $this->player = $player;
        $this->isMuted = $plugin->isMuted($player);
        $this->mode = $mode;
        $this->expires = $expires;
    }

    /**
     * Return the player to be used
     *
     * @return Player
     */
    public function getPlayer(){
        return $this->player;
    }

    /**
     * Tell is the player is already muted
     *
     * @return bool
     */
    public function isMuted(){
        return $this->isMuted;
    }

    /**
     * Tell if the player will be muted or not
     *
     * @return bool
     */
    public function willMute(){
        return $this->mode;
    }

    /**
     * Change the Mute mode to be set
     * false = Player will not be muted
     * true = Player will be muted
     *
     * @param bool $mode
     */
    public function setMuted($mode){
        if(is_bool($mode)){
            $this->mode = $mode;
        }
    }

    /**
     * Tells the time the mute state will stay
     * int = "Date Time format" of expiration
     * null = Will keep forever
     *
     * @return \DateTime|null
     */
    public function getMutedUntil(){
        return $this->expires;
    }

    /**
     * Set how long the mute will be applied
     * int = "Date Time format" of expiration
     * null = Will keep forever
     *
     * @param \DateTime|null $expires
     */
    public function setMutedUntil(\DateTime $expires = null){
        $this->expires = $expires;
    }
} 