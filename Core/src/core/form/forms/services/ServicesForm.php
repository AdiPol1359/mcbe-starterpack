<?php

namespace core\form\forms\services;

use core\form\forms\Error;
use core\form\BaseForm;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use pocketmine\Player;

class ServicesForm extends BaseForm {
    public function __construct(Player $player) {
        $data = [
            "type" => "form",
            "title" => "§l§9USLUGI",
            "content" => "",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Twoje uslugi §8§l«§r\n§8Kliknij aby zobaczyc"];

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG . "service"))
            $data["buttons"][] = ["text" => "§8§l» §9Logi uslug §8§l«§r\n§8Kliknij aby zobaczyc"];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        switch($data) {
            case "0":
                if(!UserManager::getUser($player->getName())->hasService())
                    $player->sendForm(new Error($player, "Nie masz zadnych wykupionych uslug!", $this));
                else
                    $player->sendForm(new ServiceListForm($player));
                break;
            case "1":

                $player->sendForm(new ServiceLogForm());
                break;
        }
    }
}