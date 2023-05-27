<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;

class VipCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("vip", "", false, true);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        $sender->sendMessage(MessageUtil::formatLines(Settings::$VIP_DESCRIPTION, "§l§eRANGA VIP"));
    }
}