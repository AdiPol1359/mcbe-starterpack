<?php

namespace core\form\forms;

use core\fakeinventory\inventory\SettingsInventory;
use core\form\BaseForm;
use pocketmine\Player;

class JoinForm extends BaseForm {

    public function __construct() {
        $data = [
            "type" => "modal",
            "title" => "§9§lWitaj§8!",
            "content" => "§9§lWitaj§r§7, Na serwerze §9§lDarkMoonPE.PL§r§7 mamy nadzieje ze ci sie tutaj spodoba, przydatne komendy znajdziesz pod §l§8/§9pomoc §r§7a komende od zarzadzania jaskinia pod §l§8/§9caveblock§r§7. Ale zanim rozpoczniesz z nami swoja przygode, zalecamy wlaczenie lub wylaczenie roznych funckji wygladowych aby dostosowac wyglad pod swoje preferencje klikajac w przyciski ponizej. §l§9Milej gry!",
            "button1" => "§9§lUstawienia",
            "button2" => "§8§8Graj na domyslnych ustawieniach"
        ];
        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {
        if($data == 1)
            (new SettingsInventory($player))->openFor([$player]);
    }
}