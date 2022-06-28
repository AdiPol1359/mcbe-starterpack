<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Sudo extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "sudo", "Run a command as another player", "/sudo <player> <command line|c:<chat message>");
        $this->setPermission("essentials.sudo.use");
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
            $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
            return false;
        }
        $player = $this->getPlugin()->getPlayer(array_shift($args));
        if(!$player){
            $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
            return false;
        }elseif($player->hasPermission("essentials.sudo.exempt")){
            $sender->sendMessage(TextFormat::RED . "[Error] " . $player->getName() . " cannot be sudo'ed");
            return false;
        }

        $v = implode(" ", $args);
        if(substr($v, 0, 2) === "c:"){
            $sender->sendMessage(TextFormat::GREEN . "Sending message as " .  $player->getDisplayName());
            $this->getPlugin()->getServer()->getPluginManager()->callEvent($ev = new PlayerChatEvent($player, substr($v, 2)));
            if(!$ev->isCancelled()){
                $this->getPlugin()->getServer()->broadcastMessage(\sprintf($ev->getFormat(), $ev->getPlayer()->getDisplayName(), $ev->getMessage()), $ev->getRecipients());
            }
        }else{
            $sender->sendMessage(TextFormat::AQUA . "Command ran as " .  $player->getDisplayName());
            $this->getPlugin()->getServer()->dispatchCommand($player, $v);
        }
        return true;
    }
} 
