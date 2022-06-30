<?php

namespace core\form\forms\shop\normal;

use core\form\BaseForm;
use core\form\forms\Error;
use core\Main;
use core\manager\managers\ServerManager;
use core\user\UserManager;
use pocketmine\item\Item;
use pocketmine\Player;

class ShopForm extends BaseForm {

    private string $formName;

    public function __construct(string $formName) {
        $formData = [
            "type" => "form",
            "title" => "",
            "content" => "",
            "buttons" => []
        ];

        $data = Main::getShopConfig()->get("forms")[$formName];

        if(isset($data["title"]))
            $formData["title"] = $data["title"];

        foreach($data["buttons"] as $buttonData) {

            $buttonName = $buttonData["text"];

            foreach($buttonData["onClick"] as $actionName => $data) {
                switch($actionName) {
                    case "sell":
                        $sellItemData = explode(':', $data["sellItem"]);

                        if($sellItemData[2] > 0) {
                            $count = $sellItemData[2];
                            $buttonName .= "\n" . "§r§8x§l§9".$count;
                        }

                        break;
                    case "buy":
                        $buyItemData = explode(':', $data["buyItem"]);

                        if($buyItemData[2] > 0) {
                            $count = $buyItemData[2];
                            $buttonName .= "\n" . "§r§8x§l§9".$count;
                        }
                        break;
                }
            }

            $button = ["text" => $buttonName];

            if(isset($buttonData["image"]))
                if(in_array($buttonData["image"]["type"], ["path", "url"]))
                    $button["image"] = ["type" => $buttonData["image"]["type"], "data" => $buttonData["image"]["data"]];

            $formData["buttons"][] = $button;
        }

        $this->formName = $formName;
        $this->data = $formData;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        if(!ServerManager::isSettingEnabled(ServerManager::SHOP)) {
            $player->sendForm(new Error($player, "Sklep jest aktualnie wylaczony!", $this));
            return;
        }

        $title = Main::getShopConfig()->get("forms")[$this->formName]["buttons"][intval($data)]["text"];
        $onClickData = Main::getShopConfig()->get("forms")[$this->formName]["buttons"][intval($data)]["onClick"];

        foreach($onClickData as $actionName => $data) {
            switch($actionName) {
                case "send":
                    $player->sendForm(new ShopForm($data));
                    break;

                case "sell":
                    $sellItemData = explode(':', $data["sellItem"]);

                    $sellItem = Item::get((int) $sellItemData[0], (int) $sellItemData[1]);
                    $addMoney = $data["addMoney"];
                    $count = $sellItemData[2];

                    if($count > 1){

                        $sellItem->setCount($count);

                        $userManager = UserManager::getUser($player->getName());

                        if(!$player->getInventory()->contains($sellItem)) {
                            $player->sendForm(new Error($player, "Nie masz wystarczajaco duzej ilosci tego przedmiotu aby go sprzedac!", $this));
                            return;
                        }

                        $player->sendForm(new ShopConfirmForm(ShopConfirmForm::SELL, $userManager, $addMoney, $sellItem, (int)$count, $this));
                        return;
                    }

                    $player->sendForm(new SellForm($title, $sellItem, $addMoney, $this));
                    break;

                case "buy":

                    $payCost = $data["payItem"];
                    $buyItemData = explode(':', $data["buyItem"]);

                    $buyItem = Item::get((int) $buyItemData[0], (int) $buyItemData[1]);
                    if(isset($buyItemData[3]))
                        $buyItem->setCustomName($buyItemData[3]);

                    $count = $buyItemData[2];

                    if($count > 1){

                        $buyItem->setCount($count);

                        $userManager = UserManager::getUser($player->getName());

                        if($userManager->getPlayerMoney() < $payCost) {
                            $player->sendForm(new Error($player, "Nie masz wystarczajaco duzej ilosci pieniedzy aby kupic ten przedmiot!", $this));
                            return;
                        }

                        $player->sendForm(new ShopConfirmForm(ShopConfirmForm::BUY, $userManager, $payCost, $buyItem, (int)$count, $this));
                        return;
                    }

                    $player->sendForm(new BuyForm($title, $buyItem, $payCost, $this));
                    break;
            }
        }
    }
}