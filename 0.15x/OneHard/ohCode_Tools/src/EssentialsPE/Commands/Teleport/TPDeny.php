<?php
namespace EssentialsPE\Commands\Teleport;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TPDeny extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "tpdeny", "Decline a Teleport Request", "/tpdeny [player]", false, ["tpno"]);
        $this->setPermission("essentials.tpdeny");
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
        $request = $this->getPlugin()->hasARequest($sender);
        if(!$request){
            $sender->sendMessage(TextFormat::RED . "[Error] You don't have any request yet");
            return false;
        }
        switch(count($args)){
            case 0:
                $player = $this->getPlugin()->getPlayer(($name = $this->getPlugin()->getLatestRequest($sender)));
                if(!$player){
                    $sender->sendMessage(TextFormat::RED . "[Error] Request unavailable");
                    return false;
                }
                $player->sendMessage(TextFormat::AQUA . $sender->getDisplayName() . TextFormat::RED . " denied your teleport request");
                $sender->sendMessage(TextFormat::GREEN . "Denied " . TextFormat::AQUA . $player->getName() . (substr($player->getDisplayName(), -1, 1) === "s" ? "'" : "'s") . TextFormat::RED . " teleport request");
                $this->getPlugin()->removeTPRequest($player, $sender);
                break;
            case 1:
                $player = $this->getPlugin()->getPlayer($args[0]);
                if(!$player) {
                    $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
                    return false;
                }
                if(!($request = $this->getPlugin()->hasARequestFrom($sender, $player))){
                    $sender->sendMessage(TextFormat::RED . "[Error] You don't have any requests from " . TextFormat::AQUA . $player->getName());
                    return false;
                }
                $player->sendMessage(TextFormat::AQUA . $sender->getDisplayName() . TextFormat::RED . " denied your teleport request");
                $sender->sendMessage(TextFormat::GREEN . "Denied " . TextFormat::AQUA . $player->getDisplayName() . (substr($player->getDisplayName(), -1, 1) === "s" ? "'" : "'s") . TextFormat::RED . " teleport request");
                $this->getPlugin()->removeTPRequest($player, $sender);
                break;
            default:
                $sender->sendMessage(TextFormat::RED . $this->getUsage());
                return false;
                break;
        }
        return true;
    }
} 