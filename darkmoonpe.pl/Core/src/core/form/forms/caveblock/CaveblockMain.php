<?php

namespace core\form\forms\caveblock;

use pocketmine\Player;

class CaveblockMain extends CaveForm {

    public function __construct() {

        parent::__construct();

        $data = [
            "type" => "form",
            "title" => "§9§lCAVEBLOCK",
            "content" => "",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Stworz jaskinie §8§l«§r\n§8Kliknij aby stworzyc"];
        $data["buttons"][] = ["text" => "§8§l» §9Zaproszenia §8§l«§r\n§8Kliknij aby zarzadzac"];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        switch($data) {
            case "0":
                $player->sendForm(new CreatingCave());
                break;
            case "1":
                $player->sendForm(new ChooseRequest());
                break;
        }
    }
}