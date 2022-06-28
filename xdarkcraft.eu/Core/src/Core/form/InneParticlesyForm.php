<?php

namespace Core\form;

use Core\api\LobbyAPI;
use pocketmine\Player;
use Core\Main;
use Core\api\ParticlesyAPI;

class InneParticlesyForm extends Form {
	
	public function __construct(Player $player) {
		$data = [
		 "type" => "form",
		 "title" => "§l§4Inne",
		 "content" => "",
		 "buttons" => []
		];

        $data["buttons"][] = ["text" => "Chmura\n".(ParticlesyAPI::hasParticleEnable($player, ParticlesyAPI::PARTICLE_CLOUD) ? "§l§cKLIKNIJ ABY WYLACZYC" : "§l§4KLIKNIJ ABY WLACZYC")];

		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		$formData = json_decode($data);
		
		if($formData === null) return;

		switch($formData) {
		    // DYMNA SCIEZKA
            case "0":
                if(!ParticlesyAPI::hasParticleEnable($player, ParticlesyAPI::PARTICLE_CLOUD)) {
                    ParticlesyAPI::enableParticle($player, ParticlesyAPI::PARTICLE_CLOUD, ParticlesyAPI::COLOR_NONE);
                    $player->sendMessage(Main::format("Wlaczono particlesy §4Chmura"));
                } else {
                    ParticlesyAPI::disableParticle($player, ParticlesyAPI::PARTICLE_CLOUD);
                    $player->sendMessage(Main::format("Wylaczono particlesy §4Chmura"));
                }
            break;

		}

		$player->sendForm(new InneParticlesyForm($player));
	}
}