<?php

namespace core\form\forms\caveblock;

use core\caveblock\Cave;
use core\Main;
use pocketmine\Player;

class ChoosePlayer extends CaveForm {

    public function __construct(Player $player, ?Cave $cave) {

        parent::__construct($cave);

        $data = [
            "type" => "form",
            "title" => "§l§9WYBIERZ GRACZA",
            "content" => "",
            "buttons" => []
        ];

        $buttons = [];

        $playerNames = $this->cave->getPlayers();

        Main::$sPlayer[$player->getName()] = [];

        foreach($playerNames as $nick => $settings) {
            if(!$this->cave->isOwner($nick)) {
                Main::$sPlayer[$player->getName()][] = $nick;
                $buttons[] = ["text" => "§8§l» §9".$nick." §8§l«§r"];
            }
        }

        foreach($buttons as $button)
            $data["buttons"][] = $button;

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        parent::handleResponse($player, $data);

        if($data === null) {
            $player->sendForm(new ManageCave($player, $this->cave));
            return;
        }

        Main::$selectedPlayer[$player->getName()] = Main::$sPlayer[$player->getName()][intval($data)];
        $player->sendForm(new ManagePlayer($player, $this->cave));

    }
}