<?php

namespace Core\form;

use pocketmine\Player;

use Core\Main;

class ParticlesyForm extends Form {
	
	public function __construct() {
		$data = [
		 "type" => "form",
		 "title" => "§l§4Particlesy",
		 "content" => "",
		 "buttons" => []
		];

        $data["buttons"][] = ["text" => "Sciezka"];
		$data["buttons"][] = ["text" => "Ringo"];
		$data["buttons"][] = ["text" => "Inne"];

		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		$formData = json_decode($data);
		
		if($formData === null) return;

		switch($formData) {
            case "0":
                $player->sendForm(new SciezkaParticlesyForm($player));
            break;

            case "1":
                $player->sendForm(new RingoParticlesyForm($player));
            break;

            case "2":
                $player->sendForm(new InneParticlesyForm($player));
            break;
		}
	}
}