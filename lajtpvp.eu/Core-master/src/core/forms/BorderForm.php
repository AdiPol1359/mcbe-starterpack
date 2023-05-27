<?php

declare(strict_types=1);

namespace core\forms;

use core\utils\Settings;
use pocketmine\player\Player;

class BorderForm extends BaseForm {

    public function __construct() {

        $data = [
            "type" => "custom_form",
            "title" => "§l§eBORDER",
            "content" => [],
        ];

        $data["content"][] = ["type" => "input", "text" => "§7Wielkosc borderu", "placeholder" => "800", "default" => (string)Settings::$BORDER_DATA["border"]];

        $data["content"][] = ["type" => "toggle", "text" => "§7Damage za borderem", "default" => Settings::$BORDER_DATA["damage"]];
        $data["content"][] = ["type" => "toggle", "text" => "§7Knockowanie za borderem", "default" => Settings::$BORDER_DATA["knock"]];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        Settings::$BORDER_DATA["border"] = (int)$data[0];
        Settings::$BORDER_DATA["damage"] = (bool)$data[1];
        Settings::$BORDER_DATA["knock"] = (bool)$data[2];
    }
}