<?php

namespace Core\form;

use Core\api\LobbyAPI;
use pocketmine\Player;
use Core\Main;
use Core\api\ParticlesyAPI;

class SciezkaParticlesyForm extends Form {
	
	public function __construct(Player $player) {
		$data = [
		 "type" => "form",
		 "title" => "§l§4Sciezka",
		 "content" => "",
		 "buttons" => []
		];

        $data["buttons"][] = ["text" => "Dymna sciezka\n".(ParticlesyAPI::hasParticleEnable($player, ParticlesyAPI::PARTICLE_ROAD_CLOUD) ? "§l§cKLIKNIJ ABY WYLACZYC" : "§l§4KLIKNIJ ABY WLACZYC")];
        $data["buttons"][] = ["text" => "Plomienna sciezka\n".(ParticlesyAPI::hasParticleEnable($player, ParticlesyAPI::PARTICLE_ROAD_FIRE) ? "§l§cKLIKNIJ ABY WYLACZYC" : "§l§4KLIKNIJ ABY WLACZYC")];

		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		$formData = json_decode($data);
		
		if($formData === null) return;

		switch($formData) {
		    // DYMNA SCIEZKA
            case "0":
                if(!ParticlesyAPI::hasParticleEnable($player, ParticlesyAPI::PARTICLE_ROAD_CLOUD)) {
                    ParticlesyAPI::enableParticle($player, ParticlesyAPI::PARTICLE_ROAD_CLOUD, ParticlesyAPI::COLOR_NONE);
                    $player->sendMessage(Main::format("Wlaczono particlesy §4Dymna Sciezka"));
                } else {
                    ParticlesyAPI::disableParticle($player, ParticlesyAPI::PARTICLE_ROAD_CLOUD);
                    $player->sendMessage(Main::format("Wylaczono particlesy §4Dymna Sciezka"));
                }
            break;

            case "1":
                if(!ParticlesyAPI::hasParticleEnable($player, ParticlesyAPI::PARTICLE_ROAD_FIRE)) {
                    ParticlesyAPI::enableParticle($player, ParticlesyAPI::PARTICLE_ROAD_FIRE, ParticlesyAPI::COLOR_NONE);
                    $player->sendMessage(Main::format("Wlaczono particlesy §4Plomienna Sciezka"));
                } else {
                    ParticlesyAPI::disableParticle($player, ParticlesyAPI::PARTICLE_ROAD_FIRE);
                    $player->sendMessage(Main::format("Wylaczono particlesy §4Dymna Sciezka"));
                }
            break;
		}

		$player->sendForm(new SciezkaParticlesyForm($player));
	}
}