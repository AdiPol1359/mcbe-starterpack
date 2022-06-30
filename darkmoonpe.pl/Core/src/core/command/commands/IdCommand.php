<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

class IdCommand extends BaseCommand {
    public function __construct() {
        parent::__construct("id", "Id Command", true, false, "Komenda id sluzy do pokazywania id trzymanego itemu", ["ids"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        $item = $player->getInventory()->getItemInHand();
        $player->sendMessage(MessageUtil::format("Id trzymanego przedmitu: §l§9{$item->getId()}§8:§9{$item->getDamage()}"));
    }
}