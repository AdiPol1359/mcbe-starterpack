<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Near extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "near", "List the players near to you", "/near [player]", null, ["nearby"]);
        $this->setPermission("essentials.near.use");
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
                $sender->sendMessage($this->broadcastPlayers($sender, "you"));
                break;
            case 1:
                if(!$sender->hasPermission("essentials.near.other")){
                    $sender->sendMessage(TextFormat::RED . $this->getPermissionMessage());
                    return false;
                }
                $player = $this->getPlugin()->getPlayer($args[0]);
                if(!$player){
                    $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
                    return false;
                }
                $sender->sendMessage($this->broadcastPlayers($player, $player->getDisplayName()));
                break;
            default:
                $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
                return false;
                break;
        }
        return true;
    }

    /**
     * @param Player $player
     * @param string $who
     * @return string
     */
    private function broadcastPlayers(Player $player, $who){
        $near = $this->getPlugin()->getNearPlayers($player);
        if(count($near) < 1){
            $msg = TextFormat::GRAY . "** There are no players near to " . $who . "! **";
        }else{
            $msg = TextFormat::YELLOW . "** There " . (count($near) > 1 ? "are " : "is ") . TextFormat::AQUA . count($near) . TextFormat::YELLOW . "player" . (count($near) > 1 ? "s " : " ") . "near to " . $who . ":";
            foreach($near as $p){
                $msg .= TextFormat::YELLOW . "\n* " . TextFormat::LIGHT_PURPLE . $p->getDisplayName();
            }
        }
        return $msg;
    }
} 