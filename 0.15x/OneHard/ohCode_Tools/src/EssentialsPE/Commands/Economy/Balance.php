<?php
namespace EssentialsPE\Commands\Economy;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Balance extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "balance", "See how many money do you have", "/balance [player]", null, ["bal", "money"]);
        $this->setPermission("essentials.balance.use");
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
                $sender->sendMessage(TextFormat::AQUA . "Your current balance is " . TextFormat::YELLOW . $this->getPlugin()->getCurrencySymbol() . $this->getPlugin()->getPlayerBalance($sender));
                break;
            case 1:
                if(!$sender->hasPermission("essentials.balance.other")){
                    $sender->sendMessage(TextFormat::RED . $this->getPermissionMessage());
                    return false;
                }
                $player = $this->getPlugin()->getPlayer($args[0]);
                if(!$player){
                    $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
                    return false;
                }
                $sender->sendMessage(TextFormat::AQUA . $player->getDisplayName() . " has " . TextFormat::YELLOW . $this->getPlugin()->getCurrencySymbol() . $this->getPlugin()->getPlayerBalance($player));
                break;
        }
        return true;
    }
}