<?php

namespace Core\form;

use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\enchantment\{
	Enchantment, EnchantmentInstance
};
use Core\Main;

class ShopForm extends Form {

	private $formName;
	
	public function __construct(string $formName) {
		$formData = [
		 "type" => "form",
		 "title" => "",
		 "content" => "",
		 "buttons" => []
		];
		
		$data = Main::getInstance()->getShopConfig()->get("forms")[$formName];
		
		if(isset($data["title"]))
		 $formData["title"] = $data["title"];
		
		foreach($data["buttons"] as $buttonData) {
			$button = ["text" => $buttonData["text"]];
			
			if(isset($buttonData["image"]))
			 if(in_array($buttonData["image"]["type"], ["path", "url"]))
			  $button["image"] = ["type" => $buttonData["image"]["type"], "data" => $buttonData["image"]["data"]];
			
			$formData["buttons"][] = $button;
		}
		
		$this->formName = $formName;
		$this->data = $formData;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;
		
		$onClickData = Main::getInstance()->getShopConfig()->get("forms")[$this->formName]["buttons"][intval($formData)]["onClick"];
		
		foreach($onClickData as $actionName => $data) {
			switch($actionName) {
				case "send":
				 $player->sendForm(new ShopForm($data));
				break;
				
				case "buy":
				 $payItemData = explode(':', $data["payItem"]);
				 $buyItemData = explode(':', $data["buyItem"]);
				 
				 $payItem = Item::get($payItemData[0], $payItemData[1], $payItemData[2]);
				 if(isset($payItemData[3]))
				  $payItem->setCustomName($payItemData[3]);
				 
				 if(isset($data["payItemEnchantments"])) {
				 	foreach($data["payItemEnchantments"] as $ench) {
				 		$enchData = explode(':', $ench);
				 		$enchId = $enchData[0];
				 		$enchLevel = $enchData[1];
				 		$payItem->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($enchId), $enchLevel));
				 	}
				 }
				  
				 $buyItem = Item::get($buyItemData[0], $buyItemData[1], $buyItemData[2]);
				 if(isset($buyItemData[3]))
				  $buyItem->setCustomName($buyItemData[3]);
				  
				  if(isset($data["buyItemEnchantments"])) {
				  	foreach($data["buyItemEnchantments"] as $ench) {
				 		$enchData = explode(':', $ench);
				  	$enchId = $enchData[0];
				  	
				  	if(defined(Enchantment::class."::".$enchId))
				  	 $enchId = constant(Enchantment::class.'::'.$enchId);
				  	
				  	$enchLevel = $enchData[1];
				  	$buyItem->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($enchId), $enchLevel));
				  }
				 }
				  
				 if(!$player->getInventory()->contains($payItem)) {
				 	$player->sendMessage($data["notEnoughMessage"]);
				 } else {
				 	$player->getInventory()->removeItem($payItem);
				  
				  if($player->getInventory()->canAddItem($buyItem))
				   $player->getInventory()->addItem($buyItem);
				  else
				   $player->getLevel()->dropItem($player, $buyItem);
				   
				  $player->sendMessage($data["successMessage"]);
				 }
				break;
			}
		}
	}
}