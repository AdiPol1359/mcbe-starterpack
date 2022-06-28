<?php

namespace Core\form;

use pocketmine\Player;

use pocketmine\item\Item;

use Core\Main;

class DepozytForm extends Form {
	
	public function __construct(Player $player) {
		
		$nick = $player->getName();
		
		$data = [
		 "type" => "form",
		 "title" => "Depozyt",
		 "content" => "",
		 "buttons" => []
		];
		
		$array = Main::getInstance()->getDb()->query("SELECT * FROM depozyt WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);
		
		$data["buttons"][] = ["text" => "Koxy: §4".$array["koxy"], "image" => ["type" => "path", "data" => "textures/items/apple_golden"]];
		
		$data["buttons"][] = ["text" => "Refile: §4".$array["refy"], "image" => ["type" => "path", "data" => "textures/items/apple_golden"]];
		
		$data["buttons"][] = ["text" => "Perly: §4".$array["perly"], "image" => ["type" => "path", "data" => "textures/items/ender_pearl"]];
		
		$data["buttons"][] = ["text" => "Dopelnij do limitu"];
		$data["buttons"][] = ["text" => "Wyplac wszystko"];

		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;
		
		$nick = $player->getName();
		
		$db = Main::getInstance()->getDb();
				 
		$array = $db->query("SELECT * FROM depozyt WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);
				 
		switch($formData) {
			case "0":
				if(!$array["koxy"]) {
				 $player->sendMessage("§8§l>§r §7Nie posiadasz tyle koxow w schowku!");
					return;
			 }
				
				$db->query("UPDATE depozyt SET koxy = koxy - '1' WHERE nick = '$nick'");
				
				$player->getInventory()->addItem(Item::get(466));
				
			 $player->sendMessage("§8§l>§r §7Pomyslnie wyplacono §41 §7koxa ze schowka!");
			break;
			
			case "1":
			 if(!$array["refy"]) {
	 	 	$player->sendMessage("§8§l>§r §7Nie posiadasz tyle refilow w schowku!");
			 	return;
			 }
			 
				$db->query("UPDATE depozyt SET refy = refy - '1' WHERE nick = '$nick'");
			 
			 $player->getInventory()->addItem(Item::get(322));
	 	 
			 $player->sendMessage("§8§l>§r §7Pomyslnie wyplacono §41 §7refila ze schowka!");
			break;
			
			case "2":
			 if(!$array["perly"]) {
			 	$player->sendMessage("§8§l>§r §7Nie posiadasz tyle perel w schowku!");
			 	return;
			 }
				
				$db->query("UPDATE depozyt SET perly = perly - '1' WHERE nick = '$nick'");
				
			 $player->getInventory()->addItem(Item::get(368));
				
			 $player->sendMessage("§8§l>§r §7Pomyslnie wyplacono §41 §7perle ze schowka!");
			break;
			
			case "3":
			
			 $koxy = 0;
			 $refy = 0;
			 $perly = 0;
				 	 
			 foreach($player->getInventory()->getContents() as $item) {
			 	if($item->getId() == 466)
					 $koxy += $item->getCount();
	 	 	
					if($item->getId() == 322)
	 	 	 $refy += $item->getCount();
				  
	 	 	if($item->getId() == 368)
	 	 	 $perly += $item->getCount();
	 	 }
				
	 	if($koxy < Main::LIMIT_KOXY)
				if((Main::LIMIT_KOXY - $koxy) <= $array["koxy"])
		   $koxy = Main::LIMIT_KOXY - $koxy;
				else
				  $koxy = $array["koxy"];
			 else
		   $koxy = 0;
		
			if($refy < Main::LIMIT_REFY)
				if((Main::LIMIT_REFY - $refy) <= $array["refy"])
					$refy = Main::LIMIT_REFY - $refy;
				else
				  $refy = $array["refy"];
			else
				 $refy = 0;
			  
			if($perly < Main::LIMIT_PERLY)
				if((Main::LIMIT_PERLY - $perly) <= $array["perly"])
					$perly = Main::LIMIT_PERLY - $perly;
				else
				  $perly = $array["perly"];
			else
  $perly = 0;
				 	  
			$player->getInventory()->addItem(Item::get(466, 0, $koxy));
			$player->getInventory()->addItem(Item::get(322, 0, $refy));
			$player->getInventory()->addItem(Item::get(368, 0, $perly));
			
			$db->query("UPDATE depozyt SET koxy = koxy - '$koxy' WHERE nick = '$nick'");
			$db->query("UPDATE depozyt SET refy = refy - '$refy' WHERE nick = '$nick'");
			$db->query("UPDATE depozyt SET perly = perly - '$perly' WHERE nick = '$nick'");
			
			$player->sendMessage("§8§l>§r §7Pomyslnie wyplacono §4$koxy §7koxow, §4$refy §7refow i §4$perly §7perel!");
			break;

            case "4":
                $koxy = $array['koxy'];
                $refy = $array['refy'];
                $perly = $array['perly'];

                $db->query("UPDATE depozyt SET koxy = '0' WHERE nick = '$nick'");
                $db->query("UPDATE depozyt SET refy = '0' WHERE nick = '$nick'");
                $db->query("UPDATE depozyt SET perly = '0' WHERE nick = '$nick'");

                $player->getInventory()->addItem(Item::get(466, 0, $koxy));
                $player->getInventory()->addItem(Item::get(322, 0, $refy));
                $player->getInventory()->addItem(Item::get(368, 0, $perly));

                $player->sendMessage("§8§l>§r §7Pomyslnie wyplacono §4$koxy §7koxow, §4$refy §7refow i §4$perly §7perel!");
            break;
 }
				
	 $player->sendForm(new DepozytForm($player));
	}
}