<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Kit extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "kitdddddd", "Get a pre-defined kit!", "/kitdddddddd [name] [player]", "/kitdddddd [name <player>]", ["kitsddddddd"]);
        $this->setPermission("essentials.kit.use");
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
        if($alias === "kits" || count($args) === 0){
            if(($list = $this->getPlugin()->kitList(false)) === false){
                $sender->sendMessage(TextFormat::AQUA . "There are no Kits currently available");
                return false;
            }
            $sender->sendMessage(TextFormat::AQUA . "Available kits:\n" . $list);
            return true;
        }
        $kit = $this->getPlugin()->getKit($args[0]);
        if(!$kit){
            $sender->sendMessage(TextFormat::RED . "[Error] Kit doesn't exist");
            return false;
        }
        switch(count($args)){
            case 1:
                if(!$sender instanceof Player){
                    $sender->sendMessage($this->getConsoleUsage());
                    return false;
                }
                if(!$sender->hasPermission("essentials.kits.*") && !$sender->hasPermission("essentials.kits." . strtolower($args[0]))){
                    $sender->sendMessage(TextFormat::RED . "[Error] You can't get this kit");
                    return false;
                }
                $kit->giveToPlayer($sender);
                $sender->sendMessage(TextFormat::GREEN . "Getting kit " . TextFormat::AQUA . $kit->getName() . "...");
                break;
            case 2:
                if(!$sender->hasPermission("essentials.kit.other")){
                    $sender->sendMessage(TextFormat::RED . $this->getPermissionMessage());
                    return false;
                }
                if(!$sender->hasPermission("essentials.kits.*") && !$sender->hasPermission("essentials.kits." . strtolower($args[0]))){
                    $sender->sendMessage(TextFormat::RED . "[Error] You can't get this kit");
                    return false;
                }
                $player = $this->getPlugin()->getPlayer($args[1]);
                if(!$player){
                    $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
                    return false;
                }
                $kit->giveToPlayer($player);
                $player->sendMessage(TextFormat::GREEN . "Getting kit " . TextFormat::AQUA . $kit->getName() . "...");
                $sender->sendMessage(TextFormat::GREEN . "Giving " . TextFormat::YELLOW . $player->getDisplayName() . TextFormat::GREEN . " kit " . TextFormat::AQUA . $kit->getName() . TextFormat::GREEN . "...");
                break;
            default:
                $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
                return false;
                break;
        }
        return true;
    }
}