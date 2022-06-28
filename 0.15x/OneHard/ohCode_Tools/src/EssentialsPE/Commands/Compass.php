<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Compass extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "compass", "Display your current bearing direction", "/compass", false, ["direction"]);
        $this->setPermission("essentials.compass");
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

        $direction = "";
        if($sender->getDirection() === 0){
            $direction = "south";
        }elseif($sender->getDirection() === 1){
            $direction = "west";
        }elseif($sender->getDirection() === 2){
            $direction = "north";
        }elseif($sender->getDirection() === 3){
            $direction = "east";
        }else{
            $sender->sendMessage(TextFormat::RED . "Oops, there was an error while getting your face direction");
        }

        $sender->sendMessage(TextFormat::AQUA . "You're facing " . TextFormat::YELLOW . $direction);
        return true;
    }
}
