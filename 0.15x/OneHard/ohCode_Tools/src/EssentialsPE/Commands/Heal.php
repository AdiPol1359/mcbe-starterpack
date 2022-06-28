<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\level\particle\HeartParticle;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Heal extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "heal", "Heal yourself or other player", "/heal [player]");
        $this->setPermission("essentials.heal.use");
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
                $sender->heal($sender->getMaxHealth(), new EntityRegainHealthEvent($sender, $sender->getMaxHealth() - $sender->getHealth(), EntityRegainHealthEvent::CAUSE_CUSTOM));
                $sender->getLevel()->addParticle(new HeartParticle($sender->add(0, 2), 4));
                $sender->sendMessage(TextFormat::GREEN . "You have been healed!");
                break;
            case 1:
                if(!$sender->hasPermission("essentials.heal.other")){
                    $sender->sendMessage(TextFormat::RED . $this->getPermissionMessage());
                    return false;
                }
                $player = $this->getPlugin()->getPlayer($args[0]);
                if(!$player){
                    $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
                    return false;
                }
                $player->heal($player->getMaxHealth(), new EntityRegainHealthEvent($player, $player->getMaxHealth() - $player->getHealth(), EntityRegainHealthEvent::CAUSE_CUSTOM));
                $player->getLevel()->addParticle(new HeartParticle($player->add(0, 2), 4));
                $sender->sendMessage(TextFormat::GREEN . $player->getDisplayName() . " has been healed!");
                $player->sendMessage(TextFormat::GREEN . "You have been healed!");
                break;
            default:
                $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
                return false;
                break;
        }
        return true;
    }
}
