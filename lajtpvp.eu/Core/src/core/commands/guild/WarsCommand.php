<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\guild\war\WarsInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class WarsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("wars", "", false, false, ["wojny"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        (new WarsInventory($sender))->openFor([$sender]);
    }
}