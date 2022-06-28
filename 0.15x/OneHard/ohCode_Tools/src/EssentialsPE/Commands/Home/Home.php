<?php
namespace EssentialsPE\Commands\Home;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Home extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "home", "Teleport to your home", "/home <name>", false, ["homes"]);
        $this->setPermission("essentials.home.use");
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
        if(!$sender instanceof Player){
            $sender->sendMessage($this->getConsoleUsage());
            return false;
        }
        if(count($args) > 1){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return false;
        }
        if($alias === "homes" || count($args) === 0){
            if(($list = $this->getPlugin()->homesList($sender, false)) === false){
                $sender->sendMessage(TextFormat::AQUA . "You don't have any home yet");
                return false;
            }
            $sender->sendMessage(TextFormat::AQUA . "Available homes:\n" . $list);
            return true;
        }
        $home = $this->getPlugin()->getHome($sender, $args[0]);
        if(!$home){
            $sender->sendMessage(TextFormat::RED . "[Error] Home doesn't exists or the world is not available");
            return false;
        }
        $sender->teleport($home);
        $sender->sendMessage(TextFormat::GREEN . "Teleporting to home " . TextFormat::AQUA . $home->getName() . TextFormat::GREEN . "...");
        return true;
    }
} 