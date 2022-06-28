<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Seen extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "seen", "See player's last played time", "/seen <player>");
        $this->setPermission("essentials.seen");
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
        if(count($args) !== 1){
            $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
            return false;
        }
        $player = $sender->getServer()->getOfflinePlayer($args[0]); //TODO reimplement ability to use nicks

        if($player instanceof Player){
            $sender->sendMessage(TextFormat::GREEN . $player->getDisplayName() . " is online!");
            return true;
        }
        if(!is_numeric($player->getLastPlayed())){
            $sender->sendMessage(TextFormat::RED .  $args[0] . " has never played on this server.");
            return false;
        }
        /**
         * a = am/pm
         * i = Minutes
         * h = Hour (12 hours format with leading zeros)
         * l = Day name
         * j = Day number (1 - 30/31)
         * F = Month name
         * Y = Year in 4 digits (1999)
         */
        $ptime = $player->getLastPlayed() / 1000;
        $sender->sendMessage(TextFormat::AQUA .  $player->getName() ." was last seen on " . TextFormat::RED . date("l, F j, Y", $ptime) . TextFormat::AQUA . " at " . TextFormat::RED . date("h:ia", $ptime));
        return true;
    }
}
