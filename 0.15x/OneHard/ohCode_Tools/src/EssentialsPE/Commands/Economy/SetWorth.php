<?php
namespace EssentialsPE\Commands\Economy;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SetWorth extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "setworth", "Sets the worth of the item you're holding", "/setworth <worth>", false);
        $this->setPermission("essentials.setworth");
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
        if(count($args) !== 1){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return false;
        }
        if(!is_int((int) $args[0]) || (int) $args[0] < 0){
            $sender->sendMessage(TextFormat::RED . "[Error] Please provide a valid worth");
            return false;
        }
        $sender->sendMessage(TextFormat::YELLOW . "Setting worth...");
        $id = $sender->getInventory()->getItemInHand()->getId();
        $this->getPlugin()->setItemWorth($id, (int) $args[0]);
        return true;
    }
}