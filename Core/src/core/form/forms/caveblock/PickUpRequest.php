<?php

namespace core\form\forms\caveblock;

use core\Main;
use pocketmine\Player;

class PickUpRequest extends CaveForm {

    public function __construct(Player $player) {

        parent::__construct();

        $data = [
            "type" => "form",
            "title" => "§9§lZAPROSZENIA",
            "content" => "",
            "buttons" => []
        ];

        $nick = $player->getName();

        foreach(Main::$request[$nick] as $invite)
            foreach($invite as $invitePerson => $caveTag)
                $data["buttons"][] = ["text" => "§8§l» §9".$invitePerson." §8§l«§r\n§8Tag: §9§l".$caveTag, "invitePerson" => $invitePerson, "caveTag" => $caveTag, "id" => count($data["buttons"])];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null) {
            $player->sendForm(new ChooseRequest());
            return;
        }

        foreach($this->data["buttons"] as $index)
            if($index["id"] === $data)
                Main::$selectedRequest[$player->getName()] = [$index["invitePerson"] => $index["caveTag"]];

        $player->sendForm(new RequestForm($player));
    }
}