<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\forms\BorderForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class BorderCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("border", "", true, false);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $sender->sendForm(new BorderForm());
    }
}