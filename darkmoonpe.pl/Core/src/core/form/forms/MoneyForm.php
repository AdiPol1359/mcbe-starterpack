<?php

namespace core\form\forms;

use core\form\BaseForm;
use core\form\forms\money\{
    OpenForm,
    SendForm};
use core\user\UserManager;
use pocketmine\Player;

class MoneyForm extends BaseForm {

    public function __construct(Player $player) {

        $money = UserManager::getUser($player->getName())->getPlayerMoney();

        $data = [
            "type" => "form",
            "title" => "§8Stan konta: §l§9{$money}§8zl",
            "content" => "",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Wyslij pieniadze §8§l«§r\n§8Kliknij aby wysylacac pieniadze"];
        $data["buttons"][] = ["text" => "§8§l» §9Zobacz stan konta §8§l«§r\n§8Kliknij aby zobacz stan konta"];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        switch($data) {

            case "0":
                $player->sendForm(new SendForm($player));
                break;
            case "1":
                $player->sendForm(new OpenForm($player));
                break;
        }
    }
}