<?php

namespace Core\form;

use pocketmine\Player;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

use Core\Main;

class EnchantForm extends Form {
	
	private $enchLvL;
	private $enchId;
	private $maxLvL;
	private $returnForm;
	
	public function __construct(Player $player, string $enchName, int $enchId, int $maxLvL, int $enchLvL, Form $returnForm) {
		
		$this->enchLvL = $enchLvL;
		$this->enchId = $enchId;
		$this->maxLvL = $maxLvL;
		$this->returnForm = $returnForm;
		
		$data = [
		 "type" => "form",
		 "title" => "§l§4".strtoupper($enchName),
		 "content" => "§4Ilosc biblioteczek: §4$enchLvL".PHP_EOL."Twoj level: §4".$player->getXpLevel(),
		 "buttons" => []
		];
		
		for($i = 1; $i <= $maxLvL; $i++)
		 $data["buttons"][] = ["type" => "button", "text" => $enchName." ".$this->numberFormat($i).PHP_EOL."Biblioteczki: §4".($i * 4)." §8Level: §4".($i * 10)];
	   
	   $data["buttons"][] = ["type" => "button", "text" => "Cofnij"];
	   
		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;
		
		$enchLvL = intval($formData) + 1;
		
		if($enchLvL > $this->maxLvL) {
			$player->sendForm($this->returnForm);
			return;
		}
		
		if($player->getGamemode() !== 1 && $player->getXpLevel() < ($enchLvL * 10)) {
			$player->sendMessage(Main::format("Nie masz wystarczajacego levela!"));
			return;
		}
		
		if($this->enchLvL < ($enchLvL * 4)) {
			$player->sendMessage(Main::format("Ilosc biblioteczek jest za mala!"));
			return;
		}
		
		$item = $player->getInventory()->getItemInHand();
		$enchant = new EnchantmentInstance(Enchantment::getEnchantment($this->enchId), $enchLvL);
		
		$item->addEnchantment($enchant);
		
		$player->getInventory()->setItemInHand($item);
		
		$player->sendMessage(Main::format("Pomyslnie zenchantowano item!"));
		
		if($player->getGamemode() !== 1)
		 $player->setXpLevel($player->getXpLevel() - ($enchLvL * 10));
		else
		 $player->sendMessage(Main::format("Level zostal pobrany z trybu §l§4KREATYWNEGO"));
		 
		 $player->sendForm($this);
	}
	
	private function numberFormat(int $number) {
		switch($number) {
		 case 1:
		  return "I";
		 case 2:
		  return "II";
		 case 3:
		  return "III";
		 case 4:
		  return "IV";
		 case 5:
		  return "V";
		  
		 default:
		  return "NULL";
	 }
	}
}