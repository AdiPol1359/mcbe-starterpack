<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

class DcCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("dc", "Dc Command", false, true, "Komenda sluzy do wyswietlania linka serwerowego discorda", ["discord"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        $player->sendMessage(MessageUtil::format("Link do discorda: §9§l" . ConfigUtil::DISCORD_INVITE));
    }
}