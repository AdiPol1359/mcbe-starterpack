<?php

namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class Mute extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "mute", "Prevent a player from chatting", "/mute <player> [time...]", null, ["silence"]);
        $this->setPermission("essentials.mute.use");
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
        if(count($args) < 1){
            $sender->sendMessage($this->getUsage());
            return false;
        }
        $player = $this->getPlugin()->getPlayer(array_shift($args));
        if(!$player){
            $sender->sendMessage(TextFormat::RED . "[Error] Player not found.");
            return false;
        }
        if($player->hasPermission("essentials.mute.exempt")){
            if(!$this->getPlugin()->isMuted($player)){
                $sender->sendMessage(TextFormat::RED . $player->getDisplayName() . " can't be muted");
                return false;
            }
        }
        /** @var \DateTime $date */
        $date = null;
        if(!is_bool($info = $this->getPlugin()->stringToTimestamp(implode(" ", $args)))){
            $date = $info[0];
        }
        $this->getPlugin()->switchMute($player, $date, true);
        $sender->sendMessage(TextFormat::YELLOW . $player->getDisplayName() . " has been " . ($this->getPlugin()->isMuted($player) ? "muted " . ($date !== null ? "until: " . TextFormat::AQUA . $date->format("l, F j, Y") . TextFormat::RED . " at " . TextFormat::AQUA . $date->format("h:ia") : TextFormat::AQUA . "Forever" . TextFormat::YELLOW . "!") : "unmuted!"));
        return true;
    }
} 