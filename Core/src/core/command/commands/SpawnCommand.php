<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\Main;
use core\task\tasks\TeleportTask;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;

class SpawnCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("spawn", "Spawn Command", false, false, "Komenda spawn sluzy do teleportacji na spawna", ["hub", "lobby"]);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(isset(Main::$teleportPlayers[$player->getName()])) {
            $player->sendMessage(MessageUtil::format("Jestes w trakcje teleportacji!"));
            return;
        }

        if($player->getLevel()->getName() === ConfigUtil::PVP_WORLD) {
            Main::$teleportPlayers[$player->getName()] = Main::getInstance()->getScheduler()->scheduleRepeatingTask(new TeleportTask($player->getName(), ConfigUtil::TELEPORT_TIME, Position::fromObject($this->getServer()->getDefaultLevel()->getSafeSpawn(), $this->getServer()->getDefaultLevel())), 20);
            return;
        }

        $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());

        $player->sendMessage(MessageUtil::format("Przeleteportowano na spawna"));
    }
}