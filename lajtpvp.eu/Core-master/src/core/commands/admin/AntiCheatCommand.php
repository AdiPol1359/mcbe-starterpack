<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\forms\AntiCheatForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AntiCheatCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("anticheat", "", true, false, ["ac"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $sender->sendForm(new AntiCheatForm());
    }
}