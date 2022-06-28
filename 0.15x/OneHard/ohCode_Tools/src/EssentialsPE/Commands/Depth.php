<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Depth extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "depth", "Display your depth related to sea-level", "/depth", false, ["height"]);
        $this->setPermission("essentials.depth");
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, $alias, array $args){
        if(!$this->testPermission($sender)){
            return false;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage($this->getConsoleUsage());
            return false;
        }
        if(count($args) !== 0){
            $sender->sendMessage($this->getUsage());
            return false;
        }
        $pos = $sender->getFloorY() - 63;
        if($pos === 0){
            $sender->sendMessage(TextFormat::AQUA . "You're at sea level");
        }else{
            $sender->sendMessage(TextFormat::AQUA . "You're " . (substr($pos, 0, 1) === "-" ? substr($pos, 1) : $pos) . " meters " . ($pos > 0 ? "above" : "below") . " the sea level.");
        }
        return true;
    }
}