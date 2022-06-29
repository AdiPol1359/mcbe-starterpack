<?php

namespace core\form\forms;

use core\form\forms\amoney\{
    AddMoneyForm,
    TakeMoneyForm};
use core\form\BaseForm;
use pocketmine\Player;

class AmoneyForm extends BaseForm {

    public function __construct() {

        $data = [
            "type" => "form",
            "title" => "§l§9Admin Pieniadze",
            "content" => "",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Zabierz pieniadze §8§l«§r\n§8Kliknij aby zabrac"];
        $data["buttons"][] = ["text" => "§8§l» §9Dodaj pieniadze §8§l«§r\n§8Kliknij aby dodac"];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null) return;

        switch($data) {

            case "0":
                $player->sendForm(new TakeMoneyForm());
                break;
            case "1":
                $player->sendForm(new AddMoneyForm());
                break;
        }
    }
}