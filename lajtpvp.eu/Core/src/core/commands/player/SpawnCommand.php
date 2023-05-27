<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\managers\TeleportManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\world\Position;
use pocketmine\player\Player;

class SpawnCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("spawn", "", false, false, ["hub", "lobby"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(TeleportManager::isTeleporting($sender->getName())) {
            $sender->sendMessage(MessageUtil::format("Jestes w trakcje teleportacji!"));
            return;
        }

        TeleportManager::teleport($sender, Position::fromObject($sender->getWorld()->getSafeSpawn(), $sender->getWorld()));
    }
}