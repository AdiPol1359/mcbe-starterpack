<?php

namespace Core\form;

use pocketmine\Player;

use pocketmine\item\enchantment\Enchantment;

use Core\Main;

class EnchantToolsForm extends Form {
	
	private $enchLvL;
	
	public function __construct(int $enchLvL) {
		
		$this->enchLvL = $enchLvL;
		
		$data = [
		 "type" => "form",
		 "title" => "§l§4ENCHANTING",
		 "content" => "",
		 "buttons" => []
		];
	    $data["buttons"][] = ["text" => "Efficiency (§4I §8- §4V§8)"];
	    $data["buttons"][] = ["text" => "Fortune (§4I §8- §4III§8)"];
	    $data["buttons"][] = ["text" => "Unbreaking (§4I §8- §4III§8)"];

		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;
		
		switch($formData) {
			case "0":
					$player->sendForm(new EnchantForm($player, "Efficiency", Enchantment::EFFICIENCY, 5, $this->enchLvL, $this));
			break;
			case "1":
					$player->sendForm(new EnchantForm($player, "Fortune", Enchantment::FORTUNE, 3, $this->enchLvL, $this));
			break;
			case "2":
					$player->sendForm(new EnchantForm($player, "Unbreaking", Enchantment::UNBREAKING, 3, $this->enchLvL, $this));
			break;
		}
	}
}