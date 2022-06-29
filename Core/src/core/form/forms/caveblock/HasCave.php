<?php

namespace core\form\forms\caveblock;

use core\{
    caveblock\CaveManager,
    form\forms\Error,
    form\BaseForm
};
use pocketmine\Player;

class HasCave extends BaseForm {

    public function __construct() {

        $data = [
            "type" => "form",
            "title" => "§l§9WYBIERZ OPCJE",
            "content" => "",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Zarzadzanie jaskiniami §8§l«§r\n§8Kliknij aby zarzadzac"];
        $data["buttons"][] = ["text" => "§8§l» §9Stworz jaskinie §8§l«§r\n§8Kliknij aby stworzyc"];
        $data["buttons"][] = ["text" => "§8§l» §9Zaproszenia §8§l«§r\n§8Kliknij aby zobaczyc"];
        $data["buttons"][] = ["text" => "§8§l» §9Odwiedz jaskinie §8§l«§r\n§8Kliknij aby odwiedzic"];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        switch($data) {

            case "0":
                $player->sendForm(new ChooseCave($player));
                break;
            case "1":

                if(!CaveManager::caveCount($player) > 3 && !$player->isOp()) {
                    $player->sendForm(new Error($player, "Masz za duzo jaskiń aby stworzyc nowa musisz usunac jedna!", $this));
                    return;
                }

                if(CaveManager::caveCount($player) < CaveManager::getMaxPlayerCaves($player)) {
                    $player->sendForm(new CreatingCave());
                    return;
                } else
                    $player->sendForm(new Error($player, "Osiagnales limit jaskin!", $this));
                break;

            case "2":
                $player->sendForm(new ChooseRequest());
                break;

            case "3":
                $player->sendForm(new VisitCave());
                break;
        }
    }
}