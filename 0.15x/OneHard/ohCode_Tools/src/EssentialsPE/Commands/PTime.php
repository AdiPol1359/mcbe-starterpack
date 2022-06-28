<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PTime extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "ptime", "Changes the time of a player", "/ptime <time> [player]", null, ["playertime"]);
        $this->setPermission("essentials.ptime.use");
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
            case 2:
                $static = (substr($args[0], 0, 1) === "@");
                $time = strtolower((!$static ? $args[0] : substr($args[0], 1)));
                if(!is_int($time)){
                    switch($time){
                        case "dawn":
                        case "sunrise":
                            $time = Level::TIME_SUNRISE;
                            break;
                        case "day":
                            $time = Level::TIME_DAY;
                            break;
                        case "noon":
                            $time = 6000;
                            break;
                        case "evening":
                        case "sunset":
                            $time = Level::TIME_SUNSET;
                            break;
                        case "night":
                            $time = Level::TIME_NIGHT;
                            break;
                    }
                }
                $player = $sender;
                if(isset($args[1])){
                    if(!$sender->hasPermission("essentials.ptime.other")){
                        $sender->sendMessage(TextFormat::RED . $this->getPermissionMessage());
                        return false;
                    }
                    $player = $this->getPlugin()->getPlayer($args[1]);
                    if(!$player){
                        $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
                        return false;
                    }
                }
                if(!$this->getPlugin()->setPlayerTime($player, (int) $time)){
                    $sender->sendMessage(TextFormat::RED . "Something went wrong while setting the time");
                    return false;
                }
                $sender->sendMessage(TextFormat::GREEN . "Setting player time...");
                break;
            default:
                $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
                return false;
                break;
        }
        return false;
    }
}