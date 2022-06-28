<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SetSpawn extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "setspawn", "Change your server main spawn point", "/setspawn", false);
        $this->setPermission("essentials.setspawn");
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
        if(count($args) != 0){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return false;
        }
        $sender->getLevel()->setSpawnLocation($sender);
        $sender->getServer()->setDefaultLevel($sender->getLevel());
        $sender->sendMessage(TextFormat::YELLOW . "Server's spawn point changed!");
        $sender->getServer()->getLogger()->debug(TextFormat::YELLOW . "Server's spawn point set to" . TextFormat::AQUA . $sender->getLevel()->getName() . TextFormat::YELLOW . " by " . TextFormat::GREEN . $sender->getName());
        return true;
    }
}
