<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Fly extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "fly", "Fly in Survival or Adventure mode!", "/fly [player]", null);
        $this->setPermission("essentials.fly.use");
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
                $this->getPlugin()->switchCanFly($sender);
                $sender->sendMessage("§8» §7Latanie zostalo: " . ($this->getPlugin()->canFly($sender) ? "§awlaczone" : "§cwylaczone") . "!");
				$sender->sendTip("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*\n§8»  §cPrzeczytaj chat! §«\n\n§\n§\n§");
                return true;
            case 1:
                if(!$sender->hasPermission("essentials.fly.other")){
                    $sender->sendMessage(TextFormat::RED . $this->getPermissionMessage());
                    return false;
                }
                $player = $this->getPlugin()->getPlayer($args[0]);
                if(!$player){
                    $sender->sendMessage(TextFormat::RED . "Nie ma chuja na serwerze");
					$sender->sendTip("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*\n§8»  §cPrzeczytaj chat! §«\n\n§\n§\n§");
                    return false;
                }
                $this->getPlugin()->switchCanFly($player);
                $player->sendMessage("§8» §7Latanie zostalo: " . ($this->getPlugin()->canFly($sender) ? "§awlaczone" : "§cwylaczone") . "!");
                $sender->sendMessage("§8» §7Latanie zostalo: " . ($this->getPlugin()->canFly($sender) ? "§awlaczone" : "§cwylaczone") ."!");
				$player->sendTip("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*\n§8»  §cPrzeczytaj chat! §«\n\n§\n§\n§");
				$sender->sendTip("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*\n§8»  §cPrzeczytaj chat! §«\n\n§\n§\n§");	
                break;
            default:
                $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
                return false;
                break;
        }
        return true;
    }
}