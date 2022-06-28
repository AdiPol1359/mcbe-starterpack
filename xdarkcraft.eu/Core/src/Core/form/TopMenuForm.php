<?php

namespace Core\form;

use pocketmine\Player;

use Core\Main;

class TopMenuForm extends Form {
	
	public function __construct() {
		
		$data = [
		 "type" => "form",
		 "title" => "§l§4TOPKA",
		 "content" => "",
		 "buttons" => []
		];
		
	  $data["buttons"][] = ["text" => "Topka punktow"];
	  $data["buttons"][] = ["text" => "Topka zabojstw"];
	  $data["buttons"][] = ["text" => "Topka smierci"];
	  $data["buttons"][] = ["text" => "Topka zjedzonych koxow"];
	  $data["buttons"][] = ["text" => "Topka zjedzonych refow"];
	  $data["buttons"][] = ["text" => "Topka rzuconych perel"];
	  
		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;
		
		switch($formData) {
			case "0":
			 $res = Main::getInstance()->getDb()->query("SELECT * FROM points ORDER BY points DESC LIMIT 10");
			 $top = [];
			 
			 $i = 1;
			 while($row = $res->fetchArray())
			  $top[$i++] = [$row['nick'], $row['points']];
			  
			 $player->sendForm(new TopForm("§l§4Topka punktow", $top));
			break;
			
			case "1":
			 $res = Main::getInstance()->getDb()->query("SELECT * FROM stats ORDER BY kills DESC LIMIT 10");
			 $top = [];
			 
			 $i = 1;
			 while($row = $res->fetchArray())
			  $top[$i++] = [$row['nick'], $row['kills']];
			 $player->sendForm(new TopForm("§l§4Topka zabojstw", $top));
			break;
			
			case "2":
			 $res = Main::getInstance()->getDb()->query("SELECT * FROM stats ORDER BY deaths DESC LIMIT 10");
			 $top = [];
			 
			 $i = 1;
			 while($row = $res->fetchArray())
			  $top[$i++] = [$row['nick'], $row['deaths']];
			 $player->sendForm(new TopForm("§l§4Topka smierci", $top));
			break;
			
			case "3":
			 $res = Main::getInstance()->getDb()->query("SELECT * FROM stats ORDER BY koxy DESC LIMIT 10");
			 $top = [];
			 
			 $i = 1;
			 while($row = $res->fetchArray())
			  $top[$i++] = [$row['nick'], $row['koxy']];
			 $player->sendForm(new TopForm("§l§4Topka zjedzonych koxow", $top));
			break;
			
			case "4":
			 $res = Main::getInstance()->getDb()->query("SELECT * FROM stats ORDER BY refy DESC LIMIT 10");
			 $top = [];
			 
			 $i = 1;
			 while($row = $res->fetchArray())
			  $top[$i++] = [$row['nick'], $row['refy']];
			 $player->sendForm(new TopForm("§l§4Topka zjedzonych refow", $top));
			break;
			
			case "5":
			 $res = Main::getInstance()->getDb()->query("SELECT * FROM stats ORDER BY perly DESC LIMIT 10");
			 $top = [];
			 
			 $i = 1;
			 while($row = $res->fetchArray())
			  $top[$i++] = [$row['nick'], $row['perly']];
			 $player->sendForm(new TopForm("§l§4Topka rzuconych perel", $top));
			break;
		}
	}
}