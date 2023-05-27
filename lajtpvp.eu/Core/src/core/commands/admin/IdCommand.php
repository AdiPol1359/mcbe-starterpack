<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class IdCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("id", "", true, false);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if($sender instanceof Player) {
            $item = $sender->getInventory()->getItemInHand();
            $sender->sendMessage(MessageUtil::format("Id trzymanego przedmiotu: §e{$item->getId()}§8:§e{$item->getMeta()}"));
        }
    }
}