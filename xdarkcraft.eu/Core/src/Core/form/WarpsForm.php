<?php

namespace Core\form;

use Core\task\WarpTask;
use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use Core\Main;

class WarpsForm extends Form {
	
	public function __construct() {
		
		$data = [
		 "type" => "form",
		 "title" => "§l§4Warpy",
		 "content" => "",
		 "buttons" => []
		];
		
		$result = Main::getInstance()->getDb()->query("SELECT * FROM warps");
		
		while($row = $result->fetchArray(SQLITE3_ASSOC))
		 $data["buttons"][] = ["type" => "button", "text" => $row['name']];
		 
		 if(empty($data["buttons"]))
		  $data["content"] = "§4Brak warpow!";
		 
		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {

		$formData = json_decode($data);

		if($formData === null) return;

		$api = Main::getInstance()->getWarpsAPI();

		$warp = $api->getWarpByIndex(intval($formData));

		if($warp === null) {
			$player->sendMessage(Main::format("Ups, cos poszlo nie tak!"));
			return;
		}

		if($player->hasPermission("PolishHard.warp.ignoretime")) {
            $player->teleport($api->getWarpPosition($warp));
            $player->sendMessage(Main::format("Pomyslnie przeteleportowano na warp §4$warp"));
        } else {
		    $time = Main::getInstance()->getTeleportTime($player);

            $player->addEffect(new EffectInstance(Effect::getEffect(9), 20*$time, 3));

            $player->sendMessage(Main::format("Teleportacja nastapi za §4$time §7sekund, nie ruszaj sie!"));

            if(isset(Main::$warpTask[$player->getName()]))
                Main::$warpTask[$player->getName()]->cancel();

            Main::$warpTask[$player->getName()] = Main::getInstance()->getScheduler()->scheduleDelayedTask(new WarpTask($player, $api->getWarpPosition($warp), $warp), 20*$time);

        }
	}
}