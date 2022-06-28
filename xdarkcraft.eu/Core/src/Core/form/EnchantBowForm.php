<?php

namespace Core\form;

use pocketmine\Player;

use pocketmine\item\enchantment\Enchantment;

use Core\Main;

class EnchantBowForm extends Form {
	
	private $enchLvL;
	
	public function __construct(int $enchLvL) {
		
		$this->enchLvL = $enchLvL;
		
		$data = [
		 "type" => "form",
		 "title" => "§l§4ENCHANT",
		 "content" => "",
		 "buttons" => []
		];
	    $data["buttons"][] = ["text" => "Power (§4I §8- §4V§8)"];
	    $data["buttons"][] = ["text" => "Flame (§4I §8- §4I§8)"];
	    $data["buttons"][] = ["text" => "Punch (§4I §8- §4II§8)"];
	    $data["buttons"][] = ["text" => "Unbreaking (§4I §8- §4III§8)"];

		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;
		
		switch($formData) {
			case "0":
					$player->sendForm(new EnchantForm($player, "Power", Enchantment::POWER, 5, $this->enchLvL, $this));
			break;
			case "1":
					$player->sendForm(new EnchantForm($player, "Flame", Enchantment::FLAME, 1, $this->enchLvL, $this));
			break;
			case "2":
					$player->sendForm(new EnchantForm($player, "Punch", Enchantment::PUNCH, 2, $this->enchLvL, $this));
			break;
			case "3":
					$player->sendForm(new EnchantForm($player, "Unbreaking", Enchantment::UNBREAKING, 3, $this->enchLvL, $this));
			break;
		}
	}
}