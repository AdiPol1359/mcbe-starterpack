<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Broadcast extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "broadcast", "Na glowny chat eki.", "/bc wiadmomosc", null, ["bc"]);
        $this->setPermission("essentials.broadcast");
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
        if(count($args) < 1){
            $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
            return false;
        }
		$sender->getServer()->broadcastMessage("§8");
		$sender->getServer()->broadcastMessage("§8§l*   §3§lONEHARD - PLUGINY§7§l.§f§lPL   §8§l*");
		$sender->getServer()->broadcastMessage("§8»  §cPACZKA : MARTTINEK!  §8«");
		$sender->getServer()->broadcastMessage("§8");
        $sender->getServer()->broadcastMessage("§8• §3§lCOREHC.PL§7§l ZAPRASZAMY§7§l.§f§lPL §8» §b". implode(" ", $args));
        return true;
    }
}
