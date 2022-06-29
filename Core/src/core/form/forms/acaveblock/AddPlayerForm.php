<?php

namespace core\form\forms\acaveblock;

use core\caveblock\Cave;
use core\form\forms\caveblock\CaveForm;
use core\form\forms\Error;
use core\user\UserManager;
use pocketmine\Player;

class AddPlayerForm extends CaveForm {

    public function __construct(?Cave $cave) {

        parent::__construct($cave);

        $data = [
            "type" => "custom_form",
            "title" => "§l§9ADMIN CAVEBLOCK",
            "content" => [],
        ];

        $data["content"][] = ["type" => "input", "text" => "§7Wpisz nick gracza ktorego chcesz dodac do tej jaskini!", "placeholder" => "Steve", "default" => null];
        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        parent::handleResponse($player, $data);

        $selectedUser = UserManager::getUser($data[0]);

        if(!$selectedUser){
            $player->sendForm(new Error($player, "Ten gracz nigdy nie gral na tym serwerze!", $this));
            return;
        }

        $this->cave->addPlayer($data[0]);
        $player->sendForm(new ManagePlayersForm($this->cave));
    }
}