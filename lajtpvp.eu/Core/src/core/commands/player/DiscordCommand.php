<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;

class DiscordCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("discord", "", false, true, ["dc"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        $sender->sendMessage(MessageUtil::format("Link do discorda: §e".Settings::$DISCORD_LINK));
    }
}