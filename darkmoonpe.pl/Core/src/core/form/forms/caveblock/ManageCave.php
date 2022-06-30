<?php

namespace core\form\forms\caveblock;

use core\caveblock\Cave;
use core\caveblock\CaveManager;
use core\form\forms\Error;
use core\Main;
use core\task\tasks\TeleportTask;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

class ManageCave extends CaveForm {

    public function __construct(Player $player, ?Cave $cave) {

        parent::__construct($cave);

        $data = [
            "type" => "form",
            "title" => "§8Wybrana jaskinia: §l§9" . $this->cave->getName(),
            "content" => "",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Zarzadzaj jaskinia §8§l«§r\n§8Kliknij aby zarzadzac"];
        $data["buttons"][] = ["text" => "§8§l» §9Zarzadzanie czlonkami §8§l«§r\n§8Kliknij aby zarzadzac"];
        $data["buttons"][] = ["text" => "§8§l» §9Teleport do jaskini §8§l«§r\n§8Kliknij aby sie teleportowac"];

        if($this->cave->isOwner($player->getName()))
            $data["buttons"][] = ["text" => "§8§l» §9Usun §8§l«§r\n§8Kliknij aby usunac jaskinie"];
        else
            $data["buttons"][] = ["text" => "§8§l» §9Opusc §8§l«§r\n§8Kliknij aby opuscic jaskinie"];
        $data["buttons"][] = ["text" => "§8§l» §9Cofnij §8§l«§r\n§8Kliknij aby cofnac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"]];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        parent::handleResponse($player, $data);

        switch($data) {

            case "0":
                if($this->cave->getPlayerSetting($player->getName(), "z_perm"))
                    $player->sendForm(new MainManageCave($this->cave));
                else
                    $player->sendForm(new Error($player, "Nie masz uprawnien aby to zrobic", $this));
                break;
            case "1":
                if($this->cave->getPlayerSetting($player->getName(), "z_perm")) {
                    if(count($this->cave->getPlayers()) <= 1) {
                        $player->sendForm(new Error($player, "Nie masz innych graczy w jaskini!", $this));
                        return;
                    }
                    $player->sendForm(new ChoosePlayer($player, $this->cave));
                } else
                    $player->sendForm(new Error($player, "Nie masz uprawnien aby to zrobic", $this));
                break;
            case "2":

                if(isset(Main::$teleportPlayers[$player->getName()])) {
                    $player->sendMessage(MessageUtil::format("Jestes w trakcje teleportacji!"));
                    return;
                }

                if($player->getLevel()->getName() === ConfigUtil::PVP_WORLD) {
                    Main::$teleportPlayers[$player->getName()] = Main::getInstance()->getScheduler()->scheduleRepeatingTask(new TeleportTask($player->getName(), ConfigUtil::TELEPORT_TIME, Position::fromObject($this->cave->getSpawn(), Server::getInstance()->getLevelByName($this->cave->getLevel()))), 20);
                    return;
                }

                $this->cave->teleport($player);
                break;
            case "3":
                if($this->cave->isOwner($player->getName()))
                    $player->sendForm(new Confirmation(Confirmation::DELETE_ISLE, CaveManager::getCaveByTag($this->cave->getName()), $this));
                else
                    $player->sendForm(new Confirmation(Confirmation::LEAVE_ISLE, CaveManager::getCaveByTag($this->cave->getName()), $this));
                break;
            case "4":
                $player->sendForm(new ChooseCave($player));
                break;
        }
    }
}