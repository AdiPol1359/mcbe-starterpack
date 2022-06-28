<?php
namespace EssentialsPE\Commands\Economy;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Pay extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "pay", "Pays a player from your balance", "/pay <player> <amount>", false);
        $this->setPermission("essentials.pay");
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
        if(count($args) !== 2){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return false;
        }
        $player = $this->getPlugin()->getPlayer($args[0]);
        if(!$player){
            $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
            return false;
        }
        if(substr($args[1], 0, 1) === "-"){
            $sender->sendMessage(TextFormat::RED . "[Error] You can't pay a negative value");
            return false;
        }
        $balance = $this->getPlugin()->getPlayerBalance($sender);
        $newBalance = $balance - (int) $args[1];
        if($balance < $args[1] || $newBalance < $this->getPlugin()->getMinBalance() || ($newBalance < 0 && !$player->hasPermission("essentials.eco.loan"))){
            $sender->sendMessage(TextFormat::RED . "[Error] You don't have enough money to pay");
            return false;
        }
        $sender->sendMessage(TextFormat::YELLOW . "Paying...");
        $this->getPlugin()->setPlayerBalance($sender, $newBalance); //Take out from the payer balance.
        $this->getPlugin()->addToPlayerBalance($player, (int) $args[1]); //Pay to the other player
        return true;
    }
}