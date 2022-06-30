<?php

namespace core\form\forms\caveblock;

use core\caveblock\CaveManager;
use core\form\forms\Error;
use core\Main;
use core\util\utils\MessageUtil;
use pocketmine\Player;

class RequestForm extends CaveForm {

    private string $tag;
    private string $invitePerson;

    public function __construct(Player $player) {

        foreach(Main::$selectedRequest[$player->getName()] as $invitePerson => $caveTag) {
            $this->tag = $caveTag;
            $this->invitePerson = $invitePerson;
            parent::__construct(CaveManager::getCaveByTag($caveTag));
        }

        $data = [
            "type" => "form",
            "title" => "§9§lZARZADZANIE ZAPROSZENIEM",
            "content" => "§7Zaproszenie od gracza: §9§l".$this->invitePerson."\n"."§r§7Tag jaskini: §l§9".$this->tag,
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Akceptuj §8§l«§r\n§8Kliknij aby zaakceptowac"];
        $data["buttons"][] = ["text" => "§8§l» §9Odrzuc §8§l«§r\n§8Kliknij aby odrzucic"];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        switch($data) {

            case "0":

                if(($key = array_search([$this->invitePerson => $this->tag], Main::$request[$player->getName()])) === false){
                    $player->sendForm(new Error($player, "To zaproszenie wygaslo!", new ChooseRequest()));
                    return;
                }else
                    unset(Main::$request[$player->getName()][$key]);

                $this->cave->addPlayer($player->getName());

                $player->sendMessage(MessageUtil::format("Poprawnie dolaczyles do jaskini!"));

                break;
            case "1":

                if(($key = array_search([$this->invitePerson => $this->tag], Main::$request[$player->getName()])) === false){
                    $player->sendForm(new Error($player, "To zaproszenie wygaslo!", new ChooseRequest()));
                    return;
                }else
                    unset(Main::$request[$player->getName()][$key]);

                $player->sendMessage(MessageUtil::format("Nie zaakceptowales zaproszenia do jaskini!"));

                break;
        }
    }
}