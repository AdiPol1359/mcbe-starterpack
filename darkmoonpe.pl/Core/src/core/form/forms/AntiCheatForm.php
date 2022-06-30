<?php

namespace core\form\forms;

use core\anticheat\AntiCheatManager;
use core\form\BaseForm;
use pocketmine\Player;

class AntiCheatForm extends BaseForm {

    public function __construct() {

        $data = [
            "type" => "custom_form",
            "title" => "§l§9ANTYCHEAT",
            "content" => [],
        ];

        $data["content"][] = ["type" => "toggle", "text" => "§7Anti-Noclip", "default" => AntiCheatManager::getAntiCheatByName("Noclip")->isModuleEnabled()];
        $data["content"][] = ["type" => "toggle", "text" => "§7Anti-SpeedMine", "default" => AntiCheatManager::getAntiCheatByName("FastBreak")->isModuleEnabled()];
        $data["content"][] = ["type" => "toggle", "text" => "§7Anti-Speed", "default" => AntiCheatManager::getAntiCheatByName("Speed")->isModuleEnabled()];
        $data["content"][] = ["type" => "toggle", "text" => "§7Anti-TeleportHack", "default" => AntiCheatManager::getAntiCheatByName("TeleportHack")->isModuleEnabled()];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        AntiCheatManager::getAntiCheatByName("Noclip")->setModule((bool)$data[0]);
        AntiCheatManager::getAntiCheatByName("FastBreak")->setModule((bool)$data[1]);
        AntiCheatManager::getAntiCheatByName("Speed")->setModule((bool)$data[2]);
        AntiCheatManager::getAntiCheatByName("TeleportHack")->setModule((bool)$data[3]);
    }
}