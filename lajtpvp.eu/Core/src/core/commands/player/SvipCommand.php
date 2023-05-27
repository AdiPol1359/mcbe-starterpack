<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;

class SvipCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("svip", "", false, true);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        $sender->sendMessage(MessageUtil::formatLines(Settings::$SVIP_DESCRIPTION, "§l§eRANGA SVIP"));
    }
}