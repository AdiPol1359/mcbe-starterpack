<?php
namespace EssentialsPE\BaseFiles;

use EssentialsPE\Loader;
use pocketmine\event\Listener;

class BaseEventHandler implements Listener{
    /** @var Loader */
    private $plugin;

    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        $this->plugin = $plugin;
    }

    /**
     * @return Loader
     */
    public final function getPlugin(){
        return $this->plugin;
    }
}