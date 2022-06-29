<?php

namespace core\form\forms\caveblock;

use core\{
    caveblock\CaveManager,
    form\forms\Error,
    form\BaseForm,
    Main,
    task\tasks\TeleportTask,
    util\utils\ConfigUtil,
    util\utils\MessageUtil};
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

class VisitCave extends BaseForm {

    public function __construct() {

        $data = [
            "type" => "custom_form",
            "title" => "§9§lODWIEDZANIE",
            "content" => []
        ];

        $data["content"][] = ["type" => "input", "text" => "§7Wpisz tutaj §l§9tag§r§7 jaskini ktora chcesz odwiedzic.", "placeholder" => "", "default" => null];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if(empty($data))
            return;

        if($data[0] === null)
            return;

        if(!CaveManager::existsCave($data[0])) {
            $player->sendForm(new Error($player, "Jaskinia o tym tagu nie istnieje", $this));
            return;
        }

        $cave = CaveManager::getCaveByTag(CaveManager::getCaveByTag($data[0])->getName());

        if($cave->isLocked() && !$player->hasPermission(ConfigUtil::PERMISSION_TAG . "visit.cave") && !$cave->isMember($player->getName()) && !$cave->isOwner($player->getName())) {
            $player->sendForm(new Error($player, "Jaskinia o tym tagu jest zablokowana", $this));
            return;
        }

        if(isset(Main::$teleportPlayers[$player->getName()])) {
            $player->sendMessage(MessageUtil::format("Jestes w trakcje teleportacji!"));
            return;
        }

        if($player->getLevel()->getName() === ConfigUtil::PVP_WORLD) {
            Main::$teleportPlayers[$player->getName()] = Main::getInstance()->getScheduler()->scheduleRepeatingTask(new TeleportTask($player->getName(), ConfigUtil::TELEPORT_TIME, Position::fromObject($cave->getSpawn(), Server::getInstance()->getLevelByName($cave->getLevel()))), 20);
            return;
        }

        $cave->teleport($player);
    }
}