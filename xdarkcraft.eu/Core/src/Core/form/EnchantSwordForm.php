<?php

namespace Core\form;

use pocketmine\Player;

use pocketmine\item\enchantment\Enchantment;

use Core\Main;

class EnchantSwordForm extends Form {
	
	private $enchLvL;
	
	public function __construct(int $enchLvL) {
		
		$this->enchLvL = $enchLvL;
		
		$data = [
		 "type" => "form",
		 "title" => "§l§4ENCHANT",
		 "content" => "",
		 "buttons" => []
		];
	    $data["buttons"][] = ["text" => "Sharpness (§4I §8- §4V§8)"];
	    $data["buttons"][] = ["text" => "Fire Aspect (§4I §8- §4II§8)"];
	    $data["buttons"][] = ["text" => "Knockback (§4I §8- §4II§8)"];
	    $data["buttons"][] = ["text" => "Unbreaking (§4I §8- §4III§8)"];

		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;
		
		switch($formData) {
			case "0":
					$player->sendForm(new EnchantForm($player, "Sharpness", Enchantment::SHARPNESS, 5, $this->enchLvL, $this));
			break;
			case "1":
					$player->sendForm(new EnchantForm($player, "Fire Aspect", Enchantment::FIRE_ASPECT, 2, $this->enchLvL, $this));
			break;
			case "2":
					$player->sendForm(new EnchantForm($player, "Knockback", Enchantment::KNOCKBACK, 2, $this->enchLvL, $this));
			break;
			case "3":
					$player->sendForm(new EnchantForm($player, "Unbreaking", Enchantment::UNBREAKING, 3, $this->enchLvL, $this));
			break;
		}
	}
}