<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\EffectInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EffectCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("effect", "", false, false, ["efekty", "effects"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        (new EffectInventory())->openFor([$sender]);
    }
}