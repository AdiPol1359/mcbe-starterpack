<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SetSpawnCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("setspawn", "", true, false);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $sender->sendMessage(MessageUtil::format("Ustawiles spawna swiata!"));
        $sender->getWorld()->setSpawnLocation($sender->getPosition()->round());
    }
}