<?php

namespace core\form\forms\acaveblock;

use core\caveblock\Cave;
use core\form\forms\caveblock\CaveForm;
use core\form\forms\caveblock\Confirmation;
use pocketmine\Player;

class ManageCaveForm extends CaveForm {

    public function __construct(Cave $cave) {

        parent::__construct($cave);

        $data = [
            "type" => "form",
            "title" => "§8Zarzadzasz: §l§9".$cave->getName(),
            "content" => "",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Zarzadzaj jaskinia §8§l«§r\n§8Kliknij aby zarzadzac"];
        $data["buttons"][] = ["text" => "§8§l» §9Zarzadzanie czlonkami §8§l«§r\n§8Kliknij aby zarzadzac"];
        $data["buttons"][] = ["text" => "§8§l» §9Teleport do jaskini §8§l«§r\n§8Kliknij aby sie teleportowac"];
        $data["buttons"][] = ["text" => "§8§l» §9Usun §8§l«§r\n§8Kliknij aby usunac jaskinie"];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        parent::handleResponse($player, $data);

        switch($data) {

            case "0":
                $player->sendForm(new ManageOptionsForm($this->cave));
                break;

            case "1":
                $player->sendForm(new ManagePlayersForm($this->cave));
                break;

            case "2":
                $this->cave->teleport($player);
                break;

            case "3":
                $player->sendForm(new Confirmation(Confirmation::DELETE_ISLE, $this->cave, $this));
                break;
        }
    }
}