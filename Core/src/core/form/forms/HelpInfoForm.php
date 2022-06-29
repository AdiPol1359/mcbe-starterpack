<?php

namespace core\form\forms;

use core\form\BaseForm;
use pocketmine\Player;

class HelpInfoForm extends BaseForm {

    public function __construct(string $title, string $description) {
        $data = [
            "type" => "form",
            "title" => $title,
            "content" => "§7" . $description,
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Cofnij §8§l«§r\n§8Kliknij aby cofnac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"]];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        switch($data) {
            case "0":
                $player->sendForm(new HelpForm($player));
                break;
        }
    }
}