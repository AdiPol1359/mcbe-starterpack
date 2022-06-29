<?php

namespace core\form\forms\shop\normal;

use core\form\forms\Error;
use core\form\BaseForm;
use core\manager\managers\ServerManager;
use core\user\UserManager;
use pocketmine\item\Item;
use pocketmine\Player;

class SellForm extends BaseForm {

    private BaseForm $form;
    private float $cost;
    private Item $item;

    public function __construct(string $title, Item $item, float $cost, BaseForm $form) {

        $data = [
            "type" => "custom_form",
            "title" => $title,
            "content" => []
        ];

        $data["content"][] = ["type" => "label", "text" => "§7Zarobek za jedna sztuke: §l§9" . $cost . "§r§7zl"];
        $data["content"][] = ["type" => "input", "text" => "§r§7Ilosc §l§9:", "placeholder" => "", "default" => "1"];
        $data["content"][] = ["type" => "toggle", "text" => "§r§7Sprzedac wszystko?", "default" => false];

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

        $count = $data[1];

        if($data[2] === true){
            $count = 0;
            foreach($player->getInventory()->getContents() as $index => $item){
                if($item->getId() === $this->item->getId())
                    $count += $item->getCount();
            }
        }

        if($count <= 0){
            $player->sendForm(new Error($player, "Nie posiadasz tego przedmiotu w ekwipunku!", $this));
            return;
        }

        $cost = $this->cost * $count;

        $userManager = UserManager::getUser($player->getName());

        if(!$player->getInventory()->contains($this->item)) {
            $player->sendForm(new Error($player, "Nie masz wystarczajaco duzej ilosci tego przedmiotu aby go sprzedac!", $this));
            return;
        }

        $player->sendForm(new ShopConfirmForm(ShopConfirmForm::SELL, $userManager, $cost, $this->item, (int)$count, $this->form));
    }
}