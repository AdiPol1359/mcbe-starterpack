<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender};
use Gildie\fakeinventory\GuildItemsInventory;

class ItemyCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("itemy", "Komenda itemy");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->canUse($sender))
            return;

    	(new GuildItemsInventory($sender))->send();
    }
}