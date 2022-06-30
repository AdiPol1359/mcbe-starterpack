<?php

namespace core\form\forms\acaveblock;

use core\caveblock\CaveManager;
use core\form\forms\caveblock\CaveForm;
use core\form\forms\Error;
use pocketmine\Player;

class ACaveblockMainForm extends CaveForm {

    public function __construct() {

        parent::__construct();

        $data = [
            "type" => "custom_form",
            "title" => "§l§9ADMIN CAVEBLOCK",
            "content" => [],
        ];

        $data["content"][] = ["type" => "input", "text" => "§7Wpisz tag jaskini ktora chcesz zarzadzac!", "placeholder" => "", "default" => null];
        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        $cave = CaveManager::getCaveByTag($data[0]);

        if(!$cave){
            $player->sendForm(new Error($player, "Jaskinia o takim tagu nie istnieje!", $this));
            return;
        }

        $player->sendForm(new ManageCaveForm($cave));
    }
}