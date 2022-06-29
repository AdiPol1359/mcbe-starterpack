<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\item\items\custom\Cobblex;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;

class CobblexCommand extends BaseCommand{
    public function __construct() {
        parent::__construct("cobblex", "Cobblex Command", false, false, "Komenda cobblex sluzy do kupowania cobblexa");
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(!$player->getInventory()->contains(Item::get(4, 0, 9 * 64))) {
            $player->sendMessage(MessageUtil::format("Aby zakupic §l§9CobbleX§r§7 potrzebujesz §l§99§7x§964§r §7cobblestone!"));
            return;
        }

        $player->getInventory()->removeItem(Item::get(4, 0, 9 * 64));
        $player->getInventory()->addItem(new Cobblex());

        $player->sendMessage(MessageUtil::format("Pomyslnie zakupiles §9§lCobbleX"));
    }
}