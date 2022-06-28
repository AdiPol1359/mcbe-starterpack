<?php
namespace EssentialsPE\BaseFiles;

use EssentialsPE\Loader;
use pocketmine\scheduler\PluginTask;

abstract class BaseTask extends PluginTask{
    /** @var Loader */
    private $plugin;

    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    /**
     * @return Loader
     */
    public final function getPlugin(){
        return $this->plugin;
    }
}