<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use core\utils\Settings;
use pocketmine\command\CommandSender;

class WebsiteCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("website", "", false, true, ["www", "strona"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        $sender->sendMessage(MessageUtil::format("Strona internetowa serwera: §ewww." . strtolower(Settings::$SERVER_NAME) . "§r§7!"));
    }
}