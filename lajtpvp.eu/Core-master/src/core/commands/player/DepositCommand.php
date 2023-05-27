<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\DepositInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class DepositCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("deposit", "", false, false, ["depozyt", "schowek"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        (new DepositInventory($sender))->openFor([$sender]);
    }
}