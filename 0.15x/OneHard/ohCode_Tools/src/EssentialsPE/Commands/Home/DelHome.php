<?php
namespace EssentialsPE\Commands\Home;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class DelHome extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "delhome", "Remove a home", "/delhome <name>", false, ["remhome", "removehome"]);
        $this->setPermission("essentials.delhome");
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
        if(count($args) !== 1){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return false;
        }
        if(!$this->getPlugin()->homeExists($sender, $args[0])){
            $sender->sendMessage(TextFormat::RED . "[Error] Home doesn't exist");
            return false;
        }
        $this->getPlugin()->removeHome($sender, $args[0]);
        $sender->sendMessage(TextFormat::GREEN . "Home successfuly removed!");
        return true;
    }
} 
