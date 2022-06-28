<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ClearInventory extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "clearinventory", "Clear your/other's inventory", "/clearinventory [player]", null, ["ci", "clean", "clearinvent"]);
        $this->setPermission("essentials.clearinventory.use");
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
                $sender->getInventory()->clearAll();
                $sender->sendMessage("§bCzyszczenie ekwipunku zakonczone!");
				$sender->sendTip("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*\n§8»  §cPrzeczytaj chat! §«\n\n§\n§\n§");
                break;
            case 1:
                if(!$sender->hasPermission("essentials.clearinventory.other")){
                    $sender->sendMessage(TextFormat::RED . $this->getPermissionMessage());
                    return false;
                }
                $player = $this->getPlugin()->getPlayer($args[0]);
                if(!$player){
                    $sender->sendMessage(TextFormat::RED . "Nie ma gracza!.");
                    return false;
                }
                $player->getInventory()->clearAll();
                $sender->sendMessage("§bCzyszczenie ekwipunku zakonczone!");
                $player->sendMessage("§bCzyszczenie ekwipunku zakonczone!");
				$player->sendTip("§8§l*   §3§lONE§7§lHARD§7§l.§f§lPL   §8§l*\n§8»  §cPrzeczytaj chat! §«\n\n§\n§\n§");
                break;
            default:
                $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
                return false;
                break;
        }
        return true;
    }
}
