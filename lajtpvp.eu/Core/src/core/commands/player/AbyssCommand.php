<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\AbyssInventory;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AbyssCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("abyss", "", false, false, ["otchlan"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(!Main::getInstance()->getAbyssManager()->isOpened()) {
            $sender->sendMessage(MessageUtil::format("Otchlan jest zamknieta!"));
            return;
        }

        (new AbyssInventory())->openFor([$sender]);
    }
}