<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 26/11/2016
 * Time: 17:54
 */

namespace RWCORE;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;


use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;

use pocketmine\Player;


class Enviar extends Command
{

    public $plugin;
    public $base;

    public function __construct(Loader $plugin){
        parent::__construct("enviar","enviar command");
        $this->plugin = $plugin;
        $this->base = new Base();
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {if($sender instanceof Player)
    {
        if($sender->hasPermission("vip")) {
            if (isset($args[0])) {
                $player = $sender->getServer()->getPlayer($args[0]);
                if (!$player->isOnline()) {
                    $sender->sendMessage($this->base->getPrefix() . "§cJogador offline");
                    return;
                }
                $cheio = (bool)$player->getInventory()->firstEmpty() == -1;
                if ($cheio == true) {
                    $this->base->sendError($player, 4, "", "");
                    return;
                }
                $hand = $sender->getInventory()->getItemInHand();
                if($hand->getId() == 0){
                    $player->sendMessage($this->base->getPrefix()."§cVocê não pode doar AR...");
                    return;
                }
                $item = Item::get($hand->getId(), $hand->getDamage(), $hand->getCount());
                if($hand->hasEnchantments()){
                    foreach ($hand->getEnchantments() as $ench) {
                        $id = $ench->getId();
                        $idlevel = $ench->getLevel();

                        $enchantment = Enchantment::getEnchantment($id);
                        $enchantment->setLevel($idlevel);
                        $item->addEnchantment($enchantment);
                    }
                }
                if($hand->hasCustomName()){
                    $item->setCustomName($hand->getCustomName());
                }
                if($hand->hasCustomName()) {
                    $sender->sendMessage(
                        $this->base->getPrefix() .
                        "§bvocê enviou um item para " . $player->getName() . "! Item: §6" . $hand->getCustomName()
                    );
                    $player->sendMessage(
                        $this->base->getPrefix() .
                        "§a" . $sender->getName() . "§benviou um item para você! Item: §6" . $hand->getCustomName()
                    );
                } else {
                    $sender->sendMessage(
                        $this->base->getPrefix() .
                        "§bvocê enviou um item para §6" . $player->getName() . ".§b Item: §6" . $item->getName()
                    );
                    $player->sendMessage(
                        $this->base->getPrefix() .
                        "§a" . $sender->getName() . "§b enviou um item para você! Item: §6" . $item->getName()
                    );
                }
                $sender->getInventory()->setItemInHand(new Item(0));
                $player->getInventory()->addItem($item);
            }
        }

    }}

}