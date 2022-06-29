<?php

namespace core\form\forms\caveblock;

use core\caveblock\Cave;
use core\Main;
use pocketmine\Player;

class ManagePlayerPermission extends CaveForm {

    public function __construct(Player $player, ?Cave $cave) {

        parent::__construct($cave);

        $data = [
            "type" => "custom_form",
            "title" => "§9§lZARZADZANIE PERMISJAMI",
            "content" => []
        ];

        $data["content"][] = ["type" => "toggle", "text" => "§7Interakcja z beaconem", "default" => (bool) $this->cave->getPlayerSetting(Main::$selectedPlayer[$player->getName()], "i_beacon")];
        $data["content"][] = ["type" => "toggle", "text" => "§7Otwieranie skrzyn", "default" => (bool) $this->cave->getPlayerSetting(Main::$selectedPlayer[$player->getName()], "o_chest")];
        $data["content"][] = ["type" => "toggle", "text" => "§7Stawianie blokow", "default" => (bool) $this->cave->getPlayerSetting(Main::$selectedPlayer[$player->getName()], "p_block")];
        $data["content"][] = ["type" => "toggle", "text" => "§7Niszczenie blokow", "default" => (bool) $this->cave->getPlayerSetting(Main::$selectedPlayer[$player->getName()], "b_block")];
        $data["content"][] = ["type" => "toggle", "text" => "§7Podnoszenie itemow", "default" => (bool) $this->cave->getPlayerSetting(Main::$selectedPlayer[$player->getName()], "p_item")];
        $data["content"][] = ["type" => "toggle", "text" => "§7Dropienie itemow", "default" => (bool) $this->cave->getPlayerSetting(Main::$selectedPlayer[$player->getName()], "d_item")];
        $data["content"][] = ["type" => "toggle", "text" => "§7Zarzadzanie uprawnieniami innych graczy", "default" => (bool) $this->cave->getPlayerSetting(Main::$selectedPlayer[$player->getName()], "z_perm")];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        parent::handleResponse($player, $data);

        if(!$this->cave->getPlayerSetting($player->getName(), "z_perm"))
            return;

        $this->cave->switchPlayerSetting(Main::$selectedPlayer[$player->getName()], "i_beacon", $data[0]);
        $this->cave->switchPlayerSetting(Main::$selectedPlayer[$player->getName()], "o_chest", $data[1]);
        $this->cave->switchPlayerSetting(Main::$selectedPlayer[$player->getName()], "p_block", $data[2]);
        $this->cave->switchPlayerSetting(Main::$selectedPlayer[$player->getName()], "b_block", $data[3]);
        $this->cave->switchPlayerSetting(Main::$selectedPlayer[$player->getName()], "p_item", $data[4]);
        $this->cave->switchPlayerSetting(Main::$selectedPlayer[$player->getName()], "d_item", $data[5]);
        $this->cave->switchPlayerSetting(Main::$selectedPlayer[$player->getName()], "z_perm", $data[6]);

        $player->sendForm(new ManagePlayer($player, $this->cave));
    }
}