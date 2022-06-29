<?php

namespace core\form\forms\shop\normal;

use core\form\forms\Error;
use core\form\BaseForm;
use core\manager\managers\ServerManager;
use core\user\UserManager;
use pocketmine\item\Item;
use pocketmine\Player;

class BuyForm extends BaseForm {

    private BaseForm $form;
    private float $cost;
    private Item $item;

    public function __construct(string $title, Item $item, float $cost, BaseForm $form) {

        $data = [
            "type" => "custom_form",
            "title" => $title,
            "content" => []
        ];

        $data["content"][] = ["type" => "label", "text" => "§7Koszt za jedna sztuke: §l§9" . $cost . "§r§7zl"];
        $data["content"][] = ["type" => "input", "text" => "§r§7Ilosc §l§9:", "placeholder" => "", "default" => "1"];

        $this->data = $data;
        $this->form = $form;
        $this->cost = $cost;
        $this->item = $item;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null) {
            $player->sendForm($this->form);
            return;
        }

        if(!ServerManager::isSettingEnabled(ServerManager::SHOP)) {
            $player->sendForm(new Error($player, "Sklep jest aktualnie wylaczony!", $this));
            return;
        }

        if(!ctype_digit($data[1])){
            $player->sendForm(new Error($player, "Ilosc musi byc zapisana numerycznie!", $this));
            return;
        }

        if(intval($data[1]) <= 0){
            $player->sendForm(new Error($player, "Ilosc musi byc wieksza jak 0!", $this));
            return;
        }

        if(intval($data[1]) > 10000){
            $player->sendForm(new Error($player, "Ilosc nie moze byc wieksza jak 10 000!", $this));
            return;
        }

        $cost = $this->cost * $data[1];

        $userManager = UserManager::getUser($player->getName());

        if($userManager->getPlayerMoney() < $cost){
            $player->sendForm(new Error($player, "Nie masz wystarczajaco duzo pieniedzy aby kupic ten przedmiot, brakuje ci §l§9" . abs($cost - $userManager->getPlayerMoney()) . "§7zl", $this));
            return;
        }

        $player->sendForm(new ShopConfirmForm(ShopConfirmForm::BUY, $userManager, $cost, $this->item, (int)$data[1], $this->form));
    }
}