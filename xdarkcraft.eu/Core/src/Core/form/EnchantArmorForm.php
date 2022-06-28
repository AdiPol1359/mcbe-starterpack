<?php

namespace Core\form;

use pocketmine\Player;

use pocketmine\item\enchantment\Enchantment;

use Core\Main;

class EnchantArmorForm extends Form {
	
	private $enchLvL;
	
	public function __construct(int $enchLvL, bool $featherFalling = false) {
		
		$this->enchLvL = $enchLvL;
		
		$data = [
		 "type" => "form",
		 "title" => "§l§4ENCHANTING",
		 "content" => "",
		 "buttons" => []
		];
	    $data["buttons"][] = ["text" => "Protection (§4I §8- §4IV§8)"];
	    $data["buttons"][] = ["text" => "Fire Protection (§4I §8- §4IV§8)"];
	    $data["buttons"][] = ["text" => "Thorns (§4I §8- §4III§8)"];
	    $data["buttons"][] = ["text" => "Unbreaking (§4I §8- §4III§8)"];
	    
	    if($featherFalling)
	     $data["buttons"][] = ["text" => "Feather Falling (§4I §8- §4IV§8)"];
	     
		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;
		
		switch($formData) {
			case "0":
					$player->sendForm(new EnchantForm($player, "Protection", Enchantment::PROTECTION, 4, $this->enchLvL, $this));
			break;
			case "1":
					$player->sendForm(new EnchantForm($player, "Fire Protection", Enchantment::FIRE_PROTECTION, 4, $this->enchLvL, $this));;
			break;
			case "2":
					$player->sendForm(new EnchantForm($player, "Thorns", Enchantment::THORNS, 3, $this->enchLvL, $this));
			break;
			case "3":
					$player->sendForm(new EnchantForm($player, "Unbreaking", Enchantment::UNBREAKING, 3, $this->enchLvL, $this));
			break;
			case "4":
					$player->sendForm(new EnchantForm($player, "Feather Falling", Enchantment::FEATHER_FALLING, 4, $this->enchLvL, $this));
			break;
		}
	}
}