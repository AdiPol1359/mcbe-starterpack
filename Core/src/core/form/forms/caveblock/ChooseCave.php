<?php

namespace core\form\forms\caveblock;

use core\caveblock\CaveManager;
use core\Main;
use pocketmine\Player;

class ChooseCave extends CaveForm {

    public function __construct(Player $player) {

        parent::__construct();

        $data = [
            "type" => "form",
            "title" => "§9§lWYBIERZ JASKINIE",
            "content" => "",
            "buttons" => []
        ];

        Main::$caveNames[$player->getName()] = [];

        $names = CaveManager::getCaves($player->getName());

        foreach($names as $cave) {
            Main::$caveNames[$player->getName()][] = $cave->getName();
            $data["buttons"][] = ["text" => "§8§l» §9{$cave->getName()} §8§l«§r\n§8" . CaveManager::getCaveByTag($cave->getName())->getOwner(), "id" => count($data["buttons"])];
        }

        $data["buttons"][] = ["text" => "§8§l» §9Cofnij §8§l«§r\n§8Kliknij aby cofnac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"], "id" => count($data["buttons"])];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null) {
            $player->sendForm(new HasCave());
            return;
        }

        $ids = [];
        foreach($this->data["buttons"] as $index)
            $ids[] = $index["id"];

        if(max($ids) === $data) {
            if(!CaveManager::hasCave($player))
                $player->sendForm(new CaveblockMain());
            else
                $player->sendForm(new HasCave());

            return;
        }
        $player->sendForm(new ManageCave($player, CaveManager::getCaveByTag(Main::$caveNames[$player->getName()][intval($data)])));

    }
}