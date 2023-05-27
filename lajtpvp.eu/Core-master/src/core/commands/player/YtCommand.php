<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;

class YtCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("yt", "", false, true, ["youtuber", "youtube"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        $sender->sendMessage(MessageUtil::formatLines([
            "§e/kit vip §r§8- §7Zestaw vipa",
            "§e/feed §r§8- §7Uzupelnia glod"
        ]));
    }
}