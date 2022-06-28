<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Antioch extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "antioch", "Holy hand grenade", "/antioch", false, ["grenade", "tnt"]);
        $this->setPermission("essentials.antioch");
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
        }
        if(!$this->getPlugin()->antioch($sender)){
            $sender->sendMessage(TextFormat::RED . "[Error] Cannot throw the grenade, there isn't a near valid block");
            return false;
        }
        $sender->sendMessage(TextFormat::GREEN . "Grenade threw!");
        return true;
    }
}