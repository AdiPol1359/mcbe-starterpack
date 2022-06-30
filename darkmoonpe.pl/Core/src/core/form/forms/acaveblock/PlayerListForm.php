<?php

namespace core\form\forms\acaveblock;

use core\caveblock\Cave;
use core\form\forms\caveblock\CaveForm;
use core\Main;
use pocketmine\Player;

class PlayerListForm extends CaveForm {

    public function __construct(Player $player, Cave $cave) {

        parent::__construct($cave);

        $data = [
            "type" => "form",
            "title" => "§l§9ADMIN CAVEBLOCK",
            "content" => "",
            "buttons" => []
        ];

        $buttons = [];

        $playerNames = $this->cave->getPlayers();

        Main::$sPlayer[$player->getName()] = [];

        foreach($playerNames as $nick => $settings) {
            Main::$sPlayer[$player->getName()][] = $nick;
            $buttons[] = ["text" => "§8§l» §9".$nick." §8§l«§r"];
        }

        foreach($buttons as $button)
            $data["buttons"][] = $button;

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null) {
            $player->sendForm(new ManagePlayersForm($this->cave));
            return;
        }

        parent::handleResponse($player, $data);

        Main::$selectedPlayer[$player->getName()] = Main::$sPlayer[$player->getName()][intval($data)];
        $player->sendForm(new AdminManagePlayerForm($player, $this->cave));

    }
}