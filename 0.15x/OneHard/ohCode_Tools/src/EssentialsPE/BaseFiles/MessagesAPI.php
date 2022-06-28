<?php
namespace EssentialsPE\BaseFiles;

use EssentialsPE\Loader;
use pocketmine\utils\Config;

class MessagesAPI{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        /** @var array $cfg */
        $cfg = new Config($plugin->getDataFolder() . "Messages.yml", Config::YAML);
        foreach($cfg as $type => $messages){
            switch($type){

            }
        }
    }
}