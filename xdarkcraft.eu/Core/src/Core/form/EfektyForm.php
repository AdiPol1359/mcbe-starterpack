<?php

namespace Core\form;

use pocketmine\Player;

use pocketmine\item\Item;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

use Core\Main;

class EfektyForm extends Form {
	
	public function __construct() {
			
		$data = [
		 "type" => "form",
		 "title" => "Efekty",
		 "content" => "",
		 "buttons" => []
		];
		
		$data["buttons"][] = ["text" => "Sila I \n§432 §8emeraldy §43:00", "image" => ["type" => "path", "data" => "textures/gui/newgui/mob_effects/strength_effect"]];
		
		$data["buttons"][] = ["text" => "Sila II \n§464 §8emeraldy §43:00", "image" => ["type" => "path", "data" => "textures/gui/newgui/mob_effects/strength_effect"]];
		
		$data["buttons"][] = ["text" => "Szybkosc I \n§432 §8emeraldy §43:00", "image" => ["type" => "path", "data" => "textures/gui/newgui/mob_effects/speed_effect"]];
		
		$data["buttons"][] = ["text" => "Szybkosc II \n§464 §8emeraldy §43:00", "image" => ["type" => "path", "data" => "textures/gui/newgui/mob_effects/speed_effect"]];
		
		$data["buttons"][] = ["text" => "Wysokie Skakanie I \n§432 §8emeraldy §43:00", "image" => ["type" => "path", "data" => "textures/gui/newgui/mob_effects/jump_boost_effect"]];
		
		$data["buttons"][] = ["text" => "Wysokie Skakanie II \n§464 §8emeraldy §43:00", "image" => ["type" => "path", "data" => "textures/gui/newgui/mob_effects/jump_boost_effect"]];
		
		$data["buttons"][] = ["text" => "Pospiech I \n§432 §8emeraldy §43:00", "image" => ["type" => "path", "data" => "textures/gui/newgui/mob_effects/haste_effect"]];
		
		$data["buttons"][] = ["text" => "Pospiech II \n§464 §8emeraldy §43:00", "image" => ["type" => "path", "data" => "textures/gui/newgui/mob_effects/haste_effect"]];
		
		$data["buttons"][] = ["text" => "Widzenie w ciemnosci I \n§416 §8emeraldy §43:00", "image" => ["type" => "path", "data" => "textures/gui/newgui/mob_effects/night_vision_effect"]];
		
		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;
		
		switch($formData) {
			
			//SILA I
			case "0":
			 
			 if($player->getInventory()->contains(Item::get(133, 0, 32))) {
			 	
			 	$player->getInventory()->removeItem(Item::get(133, 0, 32));
			 	
			 	$player->addEffect(new EffectInstance(Effect::getEffect(Effect::STRENGTH), 20*(60*3), 0));
			 	
   		$player->sendMessage(Main::format("Pomyslnie zakupiono efekt §4Sila I"));
			 } else
			 	$player->sendMessage(Main::format("Aby zakupic ten efekt potrzebujesz §432 §7emeraldy!"));
			break;
			
			//SILA II
			case "1":
			 
			 if($player->getInventory()->contains(Item::get(133, 0, 64))) {
			 	
			 	$player->getInventory()->removeItem(Item::get(133, 0, 64));
			 	
			 	$player->addEffect(new EffectInstance(Effect::getEffect(Effect::STRENGTH), 20*(60*3), 1));
			 	
   		$player->sendMessage(Main::format("Pomyslnie zakupiono efekt §4Sila II"));
			 } else
			 	$player->sendMessage(Main::format("Aby zakupic ten efekt potrzebujesz §464 §7emeraldy!"));
			break;
			
			//SZYBKOSC I
			case "2":
			 
			 if($player->getInventory()->contains(Item::get(133, 0, 32))) {
			 	
			 	$player->getInventory()->removeItem(Item::get(133, 0, 32));
			 	
			 	$player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 20*(60*3), 0));
			 	
   		$player->sendMessage(Main::format("Pomyslnie zakupiono efekt §4Szybkosc I"));
			 } else
			 	$player->sendMessage(Main::format("Aby zakupic ten efekt potrzebujesz §432 §7emeraldy!"));
			break;
			
			//SZYBKOSC II
			case "3":
			 
			 if($player->getInventory()->contains(Item::get(133, 0, 64))) {
			 	
			 	$player->getInventory()->removeItem(Item::get(133, 0, 64));
			 	
			 	$player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 20*(60*3), 1));
			 	
   		$player->sendMessage(Main::format("Pomyslnie zakupiono efekt §4Szybkosc II"));
			 } else
			 	$player->sendMessage(Main::format("Aby zakupic ten efekt potrzebujesz §464 §7emeraldy!"));
			break;
			
			//WYSOKIE SKAKANIE I
			case "4":
			 
			 if($player->getInventory()->contains(Item::get(133, 0, 32))) {
			 	
			 	$player->getInventory()->removeItem(Item::get(133, 0, 32));
			 	
			 	$player->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP_BOOST), 20*(60*3), 0));
			 	
   		$player->sendMessage(Main::format("Pomyslnie zakupiono efekt §4Wysokie Skakanie I"));
			 } else
			 	$player->sendMessage(Main::format("Aby zakupic ten efekt potrzebujesz §432 §7emeraldy!"));
			break;
			
			//WYSOKIE SKAKANIE II
			case "5":
			 
			 if($player->getInventory()->contains(Item::get(133, 0, 64))) {
			 	
			 	$player->getInventory()->removeItem(Item::get(133, 0, 64));
			 	
			 	$player->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP_BOOST), 20*(60*3), 1));
			 	
   		$player->sendMessage(Main::format("Pomyslnie zakupiono efekt §4Wysokie Skakanie II"));
			 } else
			 	$player->sendMessage(Main::format("Aby zakupic ten efekt potrzebujesz §464 §7emeraldy!"));
			break;
			
			//POSPIECH I
			case "6":
			 
			 if($player->getInventory()->contains(Item::get(133, 0, 32))) {
			 	
			 	$player->getInventory()->removeItem(Item::get(133, 0, 32));
			 	
			 	$player->addEffect(new EffectInstance(Effect::getEffect(Effect::HASTE), 20*(60*3), 0));
			 	
   		$player->sendMessage(Main::format("Pomyslnie zakupiono efekt §4Pospiech I"));
			 } else
			 	$player->sendMessage(Main::format("Aby zakupic ten efekt potrzebujesz §432 §7emeraldy!"));
			break;
			
			//POSPIECH II
			case "7":
			 
			 if($player->getInventory()->contains(Item::get(133, 0, 64))) {
			 	
			 	$player->getInventory()->removeItem(Item::get(133, 0, 64));
			 	
			 	$player->addEffect(new EffectInstance(Effect::getEffect(Effect::HASTE), 20*(60*3), 1));
			 	
   		$player->sendMessage(Main::format("Pomyslnie zakupiono efekt §4Pospiech II"));
			 } else
			 	$player->sendMessage(Main::format("Aby zakupic ten efekt potrzebujesz §464 §7emeraldy!"));
			break;
			
			//WIDZENIE W CIEMNOSCI I
			case "8":
			 
			 if($player->getInventory()->contains(Item::get(133, 0, 16))) {
			 	
			 	$player->getInventory()->removeItem(Item::get(133, 0, 16));
			 	
			 	$player->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 20*(60*3), 0));
			 	
   		$player->sendMessage(Main::format("Pomyslnie zakupiono efekt §4Widzenie w ciemnosci I"));
			 } else
			 	$player->sendMessage(Main::format("Aby zakupic ten efekt potrzebujesz §416 §7emeraldy!"));
			break;
		}
		
		$player->sendForm($this);
	}
}