<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class RealName extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "realname", "Check the realname of a player", "/realname <player>");
        $this->setPermission("essentials.realname");
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
        if(count($args) != 1){
            $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
            return false;
        }
        $player = $this->getPlugin()->getPlayer($args[0]);
        if(!$player){
            $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
            return false;
        }
        $sender->sendMessage(TextFormat::YELLOW .  $player->getDisplayName() . (substr($player->getName(), -1, 1) === "s" ? "'" : "'s") . " realname is: " . TextFormat::RED . $player->getName());
        return true;
    }
}
