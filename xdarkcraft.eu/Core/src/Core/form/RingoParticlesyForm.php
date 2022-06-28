<?php

namespace Core\form;

use pocketmine\Player;
use Core\Main;
use Core\api\ParticlesyAPI;

class RingoParticlesyForm extends Form {

	public function __construct(Player $player) {
		$data = [
		 "type" => "form",
		 "title" => "§l§4Wybierz kolor",
		 "content" => "",
		 "buttons" => []
		];

        $data["buttons"][] = ["text" => "§l§4ZIELONY"];
        $data["buttons"][] = ["text" => "§l§4WODNY"];
        $data["buttons"][] = ["text" => "§l§cCZERWONY"];
        $data["buttons"][] = ["text" => "§l§dROZOWY"];
        $data["buttons"][] = ["text" => "§l§eZOLTY"];
        $data["buttons"][] = ["text" => "§l§fBIALY"];
        $data["buttons"][] = ["text" => "§l§0CZARNY"];
        $data["buttons"][] = ["text" => "§l§1CIEMNY NIEBIESKI"];
        $data["buttons"][] = ["text" => "§l§4CIEMNY ZIELONY"];
        $data["buttons"][] = ["text" => "§l§3CIEMNY WODNY"];
        $data["buttons"][] = ["text" => "§l§4CIEMNY CZERWONY"];
        $data["buttons"][] = ["text" => "§l§4CIEMNY FIOLETOWY"];
        $data["buttons"][] = ["text" => "§l§6ZLOTY"];
        $data["buttons"][] = ["text" => "§l§7SZARY"];
        $data["buttons"][] = ["text" => "§l§8CIEMNY SZARY"];
        $data["buttons"][] = ["text" => "§l§9NIEBIESKI"];
        $data["buttons"][] = ["text" => "§l§4R§6A§eI§4N§9B§4O§dW"];

		if(ParticlesyAPI::hasParticleEnable($player, ParticlesyAPI::PARTICLE_RINGO))
            $data["buttons"][] = ["text" => "§l§cKLIKNIJ ABY WYLACZYC"];

		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		$formData = json_decode($data);
		
		if($formData === null) return;

		switch($formData) {

            case "0":
                $this->enableRingo($player, ParticlesyAPI::COLOR_GREEN);
            break;

            case "1":
                $this->enableRingo($player, ParticlesyAPI::COLOR_AQUA);
            break;

            case "2":
                $this->enableRingo($player, ParticlesyAPI::COLOR_RED);
            break;

            case "3":
                $this->enableRingo($player, ParticlesyAPI::COLOR_PINK);
            break;

            case "4":
                $this->enableRingo($player, ParticlesyAPI::COLOR_YELLOW);
            break;

            case "5":
                $this->enableRingo($player, ParticlesyAPI::COLOR_WHITE);
            break;

            case "6":
                $this->enableRingo($player, ParticlesyAPI::COLOR_BLACK);
            break;

            case "7":
                $this->enableRingo($player, ParticlesyAPI::COLOR_DARK_BLUE);
            break;

            case "8":
                $this->enableRingo($player, ParticlesyAPI::COLOR_DARK_GREEN);
            break;

            case "9":
                $this->enableRingo($player, ParticlesyAPI::COLOR_DARK_AQUA);
            break;

            case "10":
                $this->enableRingo($player, ParticlesyAPI::COLOR_DARK_RED);
            break;

            case "11":
                $this->enableRingo($player, ParticlesyAPI::COLOR_DARK_PURPLE);
            break;

            case "12":
                $this->enableRingo($player, ParticlesyAPI::COLOR_GOLD);
            break;

            case "13":
                $this->enableRingo($player, ParticlesyAPI::COLOR_GRAY);
            break;

            case "14":
                $this->enableRingo($player, ParticlesyAPI::COLOR_DARK_GRAY);
            break;

            case "15":
                $this->enableRingo($player, ParticlesyAPI::COLOR_BLUE);
            break;

            case "16":
                $this->enableRingo($player, ParticlesyAPI::COLOR_RAINBOW);
            break;

            case "17":
                ParticlesyAPI::disableParticle($player, ParticlesyAPI::PARTICLE_RINGO);
                $player->sendMessage(Main::format("§4Ringo §7zostalo wylaczone!"));
                unset(ParticlesyAPI::$lastRainbow[$player->getName()]);
            break;
		}

		$player->sendForm(new RingoParticlesyForm($player));
	}

	private function enableRingo(Player $player, int $color) : void {
        ParticlesyAPI::enableParticle($player, ParticlesyAPI::PARTICLE_RINGO, $color, true);
        $player->sendMessage(Main::format("§4Ringo §7zostalo wlaczone!"));
    }
}