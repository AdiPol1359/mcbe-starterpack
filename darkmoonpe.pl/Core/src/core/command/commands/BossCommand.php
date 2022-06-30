<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;

class BossCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("boss", "Boss Command", true, false, "null", []);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        $entity = Entity::createEntity("WitherBoss", $player->getLevel(), Entity::createBaseNBT($player));
        $entity->spawnToAll();
        $player->sendMessage(MessageUtil::format("Zrespiono bossa"));
    }
}