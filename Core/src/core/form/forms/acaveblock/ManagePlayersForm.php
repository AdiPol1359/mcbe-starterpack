<?php

namespace core\form\forms\acaveblock;

use core\{
    caveblock\Cave,
    form\forms\caveblock\CaveForm
};

use pocketmine\Player;

class ManagePlayersForm extends CaveForm {

    public function __construct(Cave $cave) {

        parent::__construct($cave);

        $data = [
            "type" => "form",
            "title" => "§l§9ADMIN CAVEBLOCK",
            "content" => "",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Lista czlonkow §8§l«§r\n§8Kliknij aby zobaczyc"];
        $data["buttons"][] = ["text" => "§8§l» §9Dodaj czlonka §8§l«§r\n§8Kliknij aby dodac"];
        $data["buttons"][] = ["text" => "§8§l» §9Cofnij §8§l«§r\n§8Kliknij aby cofnac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"]];
        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        parent::handleResponse($player, $data);

        switch($data) {

            case "0":
                $player->sendForm(new PlayerListForm($player, $this->cave));
                break;
            case "1":
                $player->sendForm(new AddPlayerForm($this->cave));
                break;
            case "2":
                $player->sendForm(new ManageCaveForm($this->cave));
                break;
        }
    }
}