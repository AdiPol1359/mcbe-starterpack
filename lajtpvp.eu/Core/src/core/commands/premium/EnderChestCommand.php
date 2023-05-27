<?php

declare(strict_types=1);

namespace core\commands\premium;

use core\commands\BaseCommand;
use core\inventories\FakeInventoryManager;
use core\inventories\fakeinventories\EnderChestInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EnderChestCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("enderchest", "", true, false, ["ec"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(FakeInventoryManager::isOpening($sender->getName()) || !$sender instanceof Player) {
            return;
        }

        (new EnderChestInventory($sender))->openFor([$sender]);
    }
}