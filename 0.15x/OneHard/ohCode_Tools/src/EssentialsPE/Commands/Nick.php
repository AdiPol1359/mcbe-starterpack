<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Nick extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "nick", "Change your in-game name", "/nick <new nick|off> [player]", null, ["nickname"]);
        $this->setPermission("essentials.nick.use");
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
            case 1:
                if(!$sender instanceof Player){
                    $sender->sendMessage($this->getConsoleUsage());
                    return false;
                }
                $nickname = $args[0];
                if(!($nickname === "off" ? $this->getPlugin()->removeNick($sender) : $this->getPlugin()->setNick($sender, $nickname))){
                    $sender->sendMessage(TextFormat::RED . "Invalid nick given! Please be sure to only use alphanumerical characters and underscores");
                    return false;
                }
                $sender->sendMessage(TextFormat::GREEN . "Your nick " . ($nickname === "off" ? "has been removed" : "is now " . $nickname));
                break;
            case 2:
                if(!$sender->hasPermission("essentials.nick.other")){
                    $sender->sendMessage(TextFormat::RED . $this->getPermissionMessage());
                    return false;
                }
                $player = $this->getPlugin()->getPlayer($args[1]);
                if(!$player){
                    $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
                    return false;
                }
                $nickname = $args[0];
                if(!($nickname === "off" ? $this->getPlugin()->removeNick($player) : $this->getPlugin()->setNick($player, $nickname))){
                    $sender->sendMessage(TextFormat::RED . "Invalid nick name given! Please be sure to only use alphanumerical characters and underscores");
                    return false;
                }
                $sender->sendMessage(TextFormat::GREEN . $player->getName() . (substr($player->getName(), -1, 1) === "s" ? "'" : "'s") . " nick " . ($nickname === "off" ? "has been removed" : "is now " . $nickname));
                $player->sendMessage(TextFormat::GREEN . "Your nick " . ($nickname === "off" ? "has been removed" : "is now " . $nickname));
                break;
            default:
                $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
                return false;
                break;
        }
        return true;
    }
}
