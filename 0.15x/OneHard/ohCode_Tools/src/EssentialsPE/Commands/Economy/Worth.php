<?php
namespace EssentialsPE\Commands\Economy;

use EssentialsPE\BaseFiles\BaseCommand;
use EssentialsPE\Loader;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Worth extends BaseCommand{
    /**
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin){
        parent::__construct($plugin, "worth", "Get the price of an item", "/worth <hand|item>", "/worth <item>");
        $this->setPermission("essentials.worth");
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
        if(count($args) !== 1){
            $sender->sendMessage($sender instanceof Player ? $this->getUsage() : $this->getConsoleUsage());
            return false;
        }
        switch(strtolower($args[0])){
            case "hand":
                if(!$sender instanceof Player){
                    $sender->sendMessage($this->getConsoleUsage());
                    return false;
                }
                $id = $sender->getInventory()->getItemInHand()->getId();
                $worth = $this->getPlugin()->getItemWorth($id);
                if(!$worth){
                    $sender->sendMessage(TextFormat::RED . "[Error] Worth not available for this item");
                    return false;
                }
                $sender->sendMessage(TextFormat::AQUA . "This item worth is " . $this->getPlugin()->getCurrencySymbol() . $worth);
                break;
            default:
                if(!is_int($args[0])){
                    $item = Item::fromString($args[0]);
                }else{
                    $item = Item::get($args[0]);
                }
                if($item->getId() === 0){
                    $sender->sendMessage(TextFormat::RED . "[Error] Unknown item \"" . $args[0] . "\"");
                }
                $worth = $this->getPlugin()->getItemWorth($item->getId());
                if(!$worth){
                    $sender->sendMessage(TextFormat::RED . "[Error] Worth not available for this item");
                    return false;
                }
                $sender->sendMessage(TextFormat::AQUA . "This item worth is " . $this->getPlugin()->getCurrencySymbol() . $worth);
                break;
        }
        return true;
    }
}