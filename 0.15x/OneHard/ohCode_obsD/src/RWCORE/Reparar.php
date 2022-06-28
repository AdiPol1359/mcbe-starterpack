<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 27/11/2016
 * Time: 20:17
 */

namespace RWCORE;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\Player;

use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;

class Reparar extends Command
{

    private $plugin;

    public function __construct(Loader $plugin){
        parent::__construct("reparar", "reparo comando");
        $this->plugin = $plugin;
        $this->base = new Base();
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        if($sender instanceof Player){
            $pg = $this->plugin;
            if($this->base->getEconomy()->myMoney($sender) < $pg->getConfig()->get("reparar-cost")){
                $sender->sendMessage($this->base->getPrefix()."§cDinheiro insuficiente! :(");
                return;
            }
            $hand = $sender->getInventory()->getItemInHand();
            $id = [
                260,262,263,264,265,266,280,281,282,287,288,289,
                295,296,297
            ];

            if($hand->getId() <  256 or $hand->getId() > 317 or $hand->getId() == $id){
                $sender->sendMessage($this->base->getPrefix()."§cIsso não é reparavel");
                return;
            }
            $item = Item::get($hand->getId(), 0, $hand->getCount());
            $sender->sendMessage($this->base->getPrefix()."§aVerificando se tem Nome");
            $sender->sendMessage($this->base->getPrefix()."§aVerificando se tem Encantamento");
            if($hand->hasCustomName()){
                $item->setCustomName($hand->getCustomName());
            }
            if($hand->hasEnchantments()){
                foreach($hand->getEnchantments() as $enchantment){
                    $enchID = $enchantment->getId();
                    $enchLEVEL = $enchantment->getLevel();
                    $ench = Enchantment::getEnchantment($enchID);
                    $ench->setLevel($enchLEVEL);
                    $item->addEnchantment($ench);
                }
            }
            $sender->getInventory()->setItemInHand($item);
            $sender->sendMessage($this->base->getPrefix()."§eItem Reparado");
            $this->base->getEconomy()->reduceMoney($sender, $pg->getConfig()->get("reparar-cost"));
        }
    }

}