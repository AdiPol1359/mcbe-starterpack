<?php

namespace core\form\forms;

use core\form\BaseForm;
use core\Main;
use pocketmine\Player;
use pocketmine\Server;

class AdministrationForm extends BaseForm {

    public function __construct() {
        $data = [
            "type" => "form",
            "title" => "§l§9ADMINISTRACJA",
            "content" => "",
            "buttons" => []
        ];

        $cfg = Main::getAdministration();

        foreach($cfg as $nick => $rank) {
            ($player = Server::getInstance()->getPlayerExact($nick)) ? $status = "§l§aONLINE" : $status = "§l§cOFFLINE";
            if($status === "§l§aONLINE")
                if(in_array($player->getName(), Main::$vanish))
                    $status = "§l§cOFFLINE";

            $data["buttons"][] = ["text" => "§8§l» §9{$nick} §8§l«§r\n§8$rank §l§8({$status}§8)"];
        }
        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {}
}