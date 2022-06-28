<?php
namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GetPos extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "getpos", "Get your/other's position", "/getpos [player]", null, ["coords", "position", "whereami", "getlocation", "getloc"]);
        $this->setPermission("essentials.getpos.use");
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
                $sender->sendMessage(TextFormat::GREEN . "You're in world: " . TextFormat::AQUA . $sender->getLevel()->getName() . "\n" . TextFormat::GREEN . "Your Coordinates are:" . TextFormat::YELLOW . " X: " . TextFormat::AQUA . $sender->getFloorX() . TextFormat::GREEN . "," . TextFormat::YELLOW . " Y: " . TextFormat::AQUA . $sender->getFloorY() . TextFormat::GREEN . "," . TextFormat::YELLOW . " Z: " . TextFormat::AQUA . $sender->getFloorZ());
                break;
            case 1:
                if(!$sender->hasPermission("essentials.getpos.other")){
                    $sender->sendMessage(TextFormat::RED . $this->getPermissionMessage());
                    return false;
                }
                $player = $this->getPlugin()->getPlayer($args[0]);
                if(!$player) {
                    $sender->sendMessage(TextFormat::RED . "[Error] Player not found.");
                    return false;
                }
                $sender->sendMessage(TextFormat::YELLOW . $player->getDisplayName() . TextFormat::GREEN . " is in world: " . TextFormat::AQUA . $player->getLevel()->getName() . "\n" . TextFormat::GREEN . "Coordinates:" . TextFormat::YELLOW . " X: " . TextFormat::AQUA . $player->getFloorX() . TextFormat::GREEN . "," . TextFormat::YELLOW . " Y: " . TextFormat::AQUA . $player->getFloorY() . TextFormat::GREEN . "," . TextFormat::YELLOW . " Z: " . TextFormat::AQUA . $player->getFloorZ());
                break;
            default:
                $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
                return false;
                break;
        }
        return true;
    }
}
