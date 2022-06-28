<?php

namespace Core\form;

use pocketmine\Player;

use Core\Main;

class TopForm extends Form {
	
	public function __construct(string $title, array $top) {
		
		$data = [
		 "type" => "form",
		 "title" => $title,
		 "content" => "",
		 "buttons" => []
		];
		
		$content = "";
		
		for($i = 1; $i <= 10; $i++) {
			if(isset($top[$i]))
	  $content .= "§4{$i}. §7{$top[$i][0]} §8: §4".$top[$i][1].PHP_EOL;
	  else
	   $content .= "§4{$i}. §7Brak".PHP_EOL;
	 }
	 
	 $data["content"] = $content;
	 $data["buttons"][] = ["text" => "Cofnij"];
	 
		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;
		
		switch($formData) {
			case "0":
			 $player->sendForm(new TopMenuForm());
			break;
		}
	}
}