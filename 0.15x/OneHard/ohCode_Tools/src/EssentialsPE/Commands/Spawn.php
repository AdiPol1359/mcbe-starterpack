<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Spawn extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "spawn", "Teleport to server's main spawn", "/spawn [player]", null);
        $this->setPermission("essentials.spawn.use");
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
        switch(count($args)){
            case 0:
                if(!$sender instanceof Player){
                    $sender->sendMessage($this->getConsoleUsage());
                    return false;
                }
                $sender->teleport($sender->getServer()->getDefaultLevel()->getSpawnLocation());
                $sender->sendTip("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*\n§8»  §aTeleportowanie! §«\n\n§\n§\n§");		
                break;
            case 1:
                if(!$sender->hasPermission("essentials.spawn.other")){
                    $sender->sendMessage(TextFormat::RED . "[Error] You can't teleport another one to spawn");
                    return false;
                }
                $player = $this->getPlugin()->getPlayer($args[0]);
                if(!$player) {
                    $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
                    return false;
                }
                $player->teleport($sender->getServer()->getDefaultLevel()->getSpawnLocation());
                $player->sendTip("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*\n§8»  §aTeleportowanie! §«\n\n§\n§\n§");	
                break;
            default:
                $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
                return false;
                break;
        }
        return true;
    }
} 
