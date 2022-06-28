<?php
namespace EssentialsPE\Commands\Override;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Kill extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "kill", "Kill other people", "/kill [player]", "/kill <player>");
        $this->setPermission("essentials.kill.use");
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
        if(!$sender instanceof Player && count($args) !== 1){
            $sender->sendMessage($this->getConsoleUsage());
            return false;
        }
        $player = $sender;
        if(isset($args[0])){
            if(!$sender->hasPermission("essentials.kill.other")){
                $sender->sendMessage(TextFormat::RED . $this->getPermissionMessage());
                return false;
            }
            $player = $this->getPlugin()->getPlayer($args[0]);
            if(!$player instanceof Player){
                $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
                return false;
            }
        }
        if($this->getPlugin()->isGod($player)){
            $sender->sendMessage(TextFormat::RED . "You can't kill " . $args[0]);
            return false;
        }
        $sender->getServer()->getPluginManager()->callEvent($ev = new EntityDamageEvent($player, EntityDamageEvent::CAUSE_SUICIDE, ($player->getHealth())));
        if($ev->isCancelled()){
            return true;
        }

        $player->setLastDamageCause($ev);
        $player->setHealth(0);
        $player->sendMessage("Ouch. That look like it hurt.");
        return true;
    }
} 