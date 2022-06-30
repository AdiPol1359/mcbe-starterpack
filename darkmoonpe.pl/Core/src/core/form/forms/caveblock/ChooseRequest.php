<?php

namespace core\form\forms\caveblock;

use core\caveblock\CaveManager;
use core\form\forms\Error;
use pocketmine\Player;

class ChooseRequest extends CaveForm {

    public function __construct() {

        parent::__construct();
        $data = [
            "type" => "form",
            "title" => "§l§9ZAPROSZENIA",
            "content" => "",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Odbierz §8§l«§r\n§8Kliknij aby odebrac zaproszenie"];
        $data["buttons"][] = ["text" => "§8§l» §9Zapros §8§l«§r\n§8Kliknij aby zaprosic kogos"];
        $data["buttons"][] = ["text" => "§8§l» §9Cofnij §8§l«§r\n§8Kliknij aby cofnac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"]];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        switch($data) {

            case "0":
                if(CaveManager::getCountOfRequest($player) > 0)
                    $player->sendForm(new PickUpRequest($player));
                else
                    $player->sendForm(new Error($player, "Nie masz zadnego zaproszenia!", $this));
                break;
            case "1":

                $playerCaves = [];

                foreach(CaveManager::getCaves($player->getName()) as $cave){
                    if($cave->isOwner($player->getName()) || $cave->getPlayerSetting($player->getName(), "z_perm"))
                        $playerCaves[] = $cave->getName();
                }

                if($playerCaves <= 0){
                    $player->sendForm(new Error($player, "Nie nalezysz do zadnej jaskini w ktorej masz uprawnienia do zapraszania!", $this));
                    return;
                }

                $player->sendForm(new SendRequest($player));
                break;
            case "2":
                if(!CaveManager::hasCave($player))
                    $player->sendForm(new CaveblockMain());
                else
                    $player->sendForm(new HasCave());
                break;
        }
    }
}