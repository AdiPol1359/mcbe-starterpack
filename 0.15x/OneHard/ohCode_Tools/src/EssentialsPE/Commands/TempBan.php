<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TempBan extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "tempban", "Temporary bans the specified player", "/tempban <player> <time...> [reason ...]");
        $this->setPermission("essentials.tempban");
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
        if(count($args) < 2){
            $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
            return false;
        }
        $player = $this->getPlugin()->getPlayer($name = array_shift($args));
        $info = $this->getPlugin()->stringToTimestamp(implode(" ", $args));
        if(!$info){
            $sender->sendMessage(TextFormat::RED . "[Error] Please specify a valid time");
            return false;
        }
        /** @var \DateTime $date */
        $date = $info[0];
        $reason = $info[1];
        if($player !== false){
            if($player->hasPermission("essentials.ban.exempt")){
                $sender->sendMessage(TextFormat::RED . "[Error] " . $name . " can't be banned");
                return false;
            }else{
                $name = $player->getName();
                $player->kick(TextFormat::RED . "Banned until " . TextFormat::AQUA . $date->format("l, F j, Y") . TextFormat::RED . " at " . TextFormat::AQUA . $date->format("h:ia") . (trim($reason) !== "" ? TextFormat::YELLOW . "\nReason: " . TextFormat::RESET . $reason : ""), false);
            }
        }
        $sender->getServer()->getNameBans()->addBan($name, (trim($reason) !== "" ? $reason : null), $date, "essentialspe");

        $this->broadcastCommandMessage($sender, "Banned player " . $name . " until " . $date->format("l, F j, Y") . " at " . $date->format("h:ia") . (trim($reason) !== "" ? TextFormat::YELLOW . " Reason: " . TextFormat::RESET . $reason : ""));
        return true;
    }
}
