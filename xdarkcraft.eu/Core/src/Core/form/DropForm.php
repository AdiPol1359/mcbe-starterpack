<?php

namespace Core\form;

use pocketmine\Player;

use Core\Main;

class DropForm extends Form {
	
	public function __construct(Player $player) {
				
		$nick = $player->getName();
		
		$data = [
		 "type" => "form",
		 "title" => "Drop",
		 "content" => "",
		 "buttons" => []
		];
		
		$array = Main::getInstance()->getDb()->query("SELECT * FROM 'drop' WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);

        $pearl_chance = Main::getInstance()->getDropAPI()->getChance($player, 21);

        $tnt_chance = Main::getInstance()->getDropAPI()->getChance($player, 4);

        $f_chance = Main::getInstance()->getDropAPI()->getChance($player, 5);
        $t_chance = Main::getInstance()->getDropAPI()->getChance($player, 10);
		
	    $array["diamenty"] == "on" ? $data["buttons"][] = ["text" => "Diamenty [§4ON§8]\nSzansa: {$f_chance}%%", "image" => ["type" => "path", "data" => "textures/items/diamond"]] : $data["buttons"][] = ["text" => "Diamenty [§cOFF§8]\nSzansa: {$f_chance}%%", "image" => ["type" => "path", "data" => "textures/items/diamond"]];
		$array["emeraldy"] == "on" ? $data["buttons"][] = ["text" => "Emeraldy [§4ON§8]\nSzansa: {$f_chance}%%", "image" => ["type" => "path", "data" => "textures/items/emerald"]] : $data["buttons"][] = ["text" => "Emeraldy [§cOFF§8]\nSzansa: {$f_chance}%%", "image" => ["type" => "path", "data" => "textures/items/emerald"]];
		$array["zloto"] == "on" ? $data["buttons"][] = ["text" => "Zloto [§4ON§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/items/gold_ingot"]] : $data["buttons"][] = ["text" => "Zloto [§cOFF§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/items/gold_ingot"]];
		$array["zelazo"] == "on" ? $data["buttons"][] = ["text" => "Zelazo [§4ON§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/items/iron_ingot"]] : $data["buttons"][] = ["text" => "Zelazo [§cOFF§8]\nSzansa: {$tnt_chance}%%", "image" => ["type" => "path", "data" => "textures/items/iron_ingot"]];
		$array["tnt"] == "on" ? $data["buttons"][] = ["text" => "TNT [§4ON§8]\nSzansa: {$tnt_chance}%%", "image" => ["type" => "path", "data" => "textures/blocks/tnt_side"]] : $data["buttons"][] = ["text" => "TNT [§cOFF§8]\nSzansa: {$tnt_chance}%%", "image" => ["type" => "path", "data" => "textures/blocks/tnt_side"]];
		$array["perly"] == "on" ? $data["buttons"][] = ["text" => "Perly [§4ON§8]\nSzansa: {$pearl_chance}%%", "image" => ["type" => "path", "data" => "textures/items/ender_pearl"]] : $data["buttons"][] = ["text" => "Perly [§cOFF§8]\nSzansa: {$pearl_chance}%%", "image" => ["type" => "path", "data" => "textures/items/ender_pearl"]];
		$array["slimeball"] == "on" ? $data["buttons"][] = ["text" => "SlimeBall [§4ON§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/items/slimeball"]] : $data["buttons"][] = ["text" => "SlimeBall [§cOFF§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/items/slimeball"]];
		$array["redstone"] == "on" ? $data["buttons"][] = ["text" => "Redstone [§4ON§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/items/redstone_dust"]] : $data["buttons"][] = ["text" => "Redstone [§cOFF§8]\nSzansa: {$t_chance}%%%", "image" => ["type" => "path", "data" => "textures/items/redstone_dust"]];
		$array["wegiel"] == "on" ? $data["buttons"][] = ["text" => "Wegiel [§4ON§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/items/coal"]] : $data["buttons"][] = ["text" => "Wegiel [§cOFF§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/items/coal"]];
		$array["bookshelfy"] == "on" ? $data["buttons"][] = ["text" => "Bookshelfy [§4ON§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/blocks/bookshelf"]] : $data["buttons"][] = ["text" => "Bookshelfy [§cOFF§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/blocks/bookshelf"]];
		$array["jablko"] == "on" ? $data["buttons"][] = ["text" => "Jablko [§4ON§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/items/apple"]] : $data["buttons"][] = ["text" => "Jablko [§cOFF§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/items/apple"]];
		$array["obsydian"] == "on" ? $data["buttons"][] = ["text" => "Obsydian [§4ON§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/blocks/obsidian"]] : $data["buttons"][] = ["text" => "Obsydian [§cOFF§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/blocks/obsidian"]];
		$array["nicie"] == "on" ? $data["buttons"][] = ["text" => "Nicie [§4ON§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/items/string"]] : $data["buttons"][] = ["text" => "Nicie [§cOFF§8]\nSzansa: {$t_chance}%%", "image" => ["type" => "path", "data" => "textures/items/string"]];
		$array["cobblestone"] == "on" ? $data["buttons"][] = ["text" => "Cobblestone [§4ON§8]", "image" => ["type" => "path", "data" => "textures/blocks/cobblestone"]] : $data["buttons"][] = ["text" => "Cobblestone [§cOFF§8]", "image" => ["type" => "path", "data" => "textures/blocks/cobblestone"]];
		
		$data["buttons"][] = ["text" => "Wlacz wszystkie dropy"];
		
		$data["buttons"][] = ["text" => "Wylacz wszystkie dropy"];
		
		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;
		
		$nick = $player->getName();
		
		$api = Main::getInstance()->getDropAPI();
		
		switch($formData) {
			case "0":
			 $api->switchDrop($nick, "diamenty");
			break;
			
			case "1":
			 $api->switchDrop($nick, "emeraldy");
			break;
				  	
			case "2":
				$api->switchDrop($nick, "zloto");
			break;
			
			case "3":
			 $api->switchDrop($nick, "zelazo");
			break;
				  	
  	        case "4":
			 $api->switchDrop($nick, "tnt");
			break;
			
			case "5":
			 $api->switchDrop($nick, "perly");
			break;
			
			case "6":
				$api->switchDrop($nick, "slimeball");
			break;
			
			case "7":
				$api->switchDrop($nick, "redstone");
			break;
			
			case "8":
				$api->switchDrop($nick, "wegiel");
			break;
			
			case "9":
				$api->switchDrop($nick, "bookshelfy");
			break;

			case "10":
				$api->switchDrop($nick, "jablko");
			break;

			case "11":
				$api->switchDrop($nick, "obsydian");
			break;

			case "12":
				$api->switchDrop($nick, "nicie");
			break;

			case "13":
				$api->switchDrop($nick, "cobblestone");
			break;

			case "14":
			    foreach(["diamenty", "emeraldy", "zloto", "zelazo", "tnt", "perly", "slimeball", "redstone", "wegiel", "bookshelfy", "jablko", "obsydian", "nicie", "cobblestone"] as $drop)
				    Main::getInstance()->getDb()->query("UPDATE 'drop' SET '$drop' = 'on' WHERE nick = '$nick'");
			break;
			
			case "15":
			    foreach(["diamenty", "emeraldy", "zloto", "zelazo", "tnt", "perly", "slimeball", "redstone", "wegiel", "bookshelfy", "jablko", "obsydian", "nicie", "cobblestone"] as $drop)
			 	    Main::getInstance()->getDb()->query("UPDATE 'drop' SET '$drop' = 'off' WHERE nick = '$nick'");
			break;
		}
		
		$player->sendForm(new DropForm($player));
	}
}