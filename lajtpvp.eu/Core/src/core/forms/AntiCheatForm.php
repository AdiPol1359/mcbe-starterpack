<?php

declare(strict_types=1);

namespace core\forms;

use core\Main;
use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;

class AntiCheatForm extends BaseForm {

    #[Pure] public function __construct() {

        $data = [
            "type" => "custom_form",
            "title" => "§l§eANTYCHEAT",
            "content" => [],
        ];

        $data["content"][] = ["type" => "toggle", "text" => "§7Anti-Noclip", "default" => Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("Noclip")->isModuleEnabled()];
        $data["content"][] = ["type" => "toggle", "text" => "§7Anti-SpeedMine", "default" => Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("FastBreak")->isModuleEnabled()];
        $data["content"][] = ["type" => "toggle", "text" => "§7Anti-Reach", "default" => Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("Reach")->isModuleEnabled()];
        $data["content"][] = ["type" => "toggle", "text" => "§7Anti-Blink", "default" => Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("Blink")->isModuleEnabled()];
        $data["content"][] = ["type" => "toggle", "text" => "§7Anti-Speed", "default" => Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("Speed")->isModuleEnabled()];
        $data["content"][] = ["type" => "toggle", "text" => "§7Anti-TeleportHack", "default" => Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("TeleportHack")->isModuleEnabled()];
        $data["content"][] = ["type" => "toggle", "text" => "§7Anti-AirJump", "default" => Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("TeleportHack")->isModuleEnabled()];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("Noclip")->setModule((bool)$data[0]);
        Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("FastBreak")->setModule((bool)$data[1]);
        Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("Reach")->setModule((bool)$data[2]);
        Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("Blink")->setModule((bool)$data[3]);
        Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("Speed")->setModule((bool)$data[4]);
        Main::getInstance()->getAntiCheatManager()->getAntiCheatByName("TeleportHack")->setModule((bool)$data[5]);
    }
}