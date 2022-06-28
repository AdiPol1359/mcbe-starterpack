<?php

namespace Core\form;

use pocketmine\Player;

use Core\Main;
use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

class KitsForm extends Form {

    private $kits = [];
	
	public function __construct(Player $player) {
		
		$nick = $player->getName();
		
		$api = Main::getInstance()->getKitsAPI();
			
		$data = [
		 "type" => "form",
		 "title" => "Kity",
		 "content" => "",
		 "buttons" => []
		];

		$this->kits[] = "gracz";
		if(!$api->isCooldown($nick, "gracz"))
		 $data["buttons"][] = ["text" => "§8Zestaw §4Gracz\n§l§4Kliknij aby odebrac", "image" => ["type" => "path", "data" => "textures/items/beef_cooked"]];
		else
		 $data["buttons"][] = ["text" => "§8Zestaw §4Gracz\n".$api->getCooldownFormat($nick, "gracz"), "image" => ["type" => "path", "data" => "textures/items/beef_cooked"]];
		 
		 
		if($player->hasPermission("PolishHard.kit.vip")) {
            $this->kits[] = "vip";
	 	if(!$api->isCooldown($nick, "vip"))
		  $data["buttons"][] = ["text" => "§8Zestaw §4VIP\n§l§4Kliknij aby odebrac", "image" => ["type" => "path", "data" => "textures/items/iron_helmet"]];
	 	else
		  $data["buttons"][] = ["text" => "§8Zestaw §4VIP\n".$api->getCooldownFormat($nick, "vip"), "image" => ["type" => "path", "data" => "textures/items/iron_helmet"]];
		}
		
		if($player->hasPermission("PolishHard.kit.svip")) {
            $this->kits[] = "svip";
	 	if(!$api->isCooldown($nick, "svip"))
		  $data["buttons"][] = ["text" => "§8Zestaw §4SVIP\n§l§4Kliknij aby odebrac", "image" => ["type" => "path", "data" => "textures/items/gold_helmet"]];
		 else
		  $data["buttons"][] = ["text" => "§8Zestaw §4SVIP\n".$api->getCooldownFormat($nick, "svip"), "image" => ["type" => "path", "data" => "textures/items/gold_helmet"]];
	}
	
	 if($player->hasPermission("PolishHard.kit.sponsor")) {
         $this->kits[] = "sponsor";
		 if(!$api->isCooldown($nick, "sponsor"))
		  $data["buttons"][] = ["text" => "§8Zestaw §4SPONSOR\n§l§4Kliknij aby odebrac", "image" => ["type" => "path", "data" => "textures/items/diamond_helmet"]];
	 	else
		  $data["buttons"][] = ["text" => "§8Zestaw §4SPONSOR\n".$api->getCooldownFormat($nick, "sponsor"), "image" => ["type" => "path", "data" => "textures/items/diamond_helmet"]];
		}

        if($player->hasPermission("PolishHard.kit.tnt")) {
            $this->kits[] = "tnt";
            if(!$api->isCooldown($nick, "tnt"))
                $data["buttons"][] = ["text" => "§8Zestaw §4TNT\n§l§4Kliknij aby odebrac", "image" => ["type" => "path", "data" => "textures/blocks/tnt_side"]];
            else
                $data["buttons"][] = ["text" => "§8Zestaw §4TNT\n".$api->getCooldownFormat($nick, "tnt"), "image" => ["type" => "path", "data" => "textures/blocks/tnt_side"]];
        }
		
		if($player->hasPermission("PolishHard.kit.yt")) {
            $this->kits[] = "yt";
	 	if(!$api->isCooldown($nick, "yt"))
	 	 $data["buttons"][] = ["text" => "§8Zestaw §4YT\n§l§4Kliknij aby odebrac", "image" => ["type" => "path", "data" => "textures/items/leather_helmet"]];
		 else
		  $data["buttons"][] = ["text" => "§8Zestaw §4YT\n".$api->getCooldownFormat($nick, "yt"), "image" => ["type" => "path", "data" => "textures/items/leather_helmet"]];
		}
		 
		if($player->hasPermission("PolishHard.kit.yt+")) {
            $this->kits[] = "yt+";
		 if(!$api->isCooldown($nick, "yt+"))
		  $data["buttons"][] = ["text" => "§8Zestaw §4YT+\n§l§4Kliknij aby odebrac", "image" => ["type" => "path", "data" => "textures/items/chainmail_helmet"]];
	 	else
		  $data["buttons"][] = ["text" => "§8Zestaw §4YT+\n".$api->getCooldownFormat($nick, "yt+"), "image" => ["type" => "path", "data" => "textures/items/chainmail_helmet"]];
		}

		$this->data = $data;
	}
	
	public function handleResponse(Player $player, $data) : void {
		
		$formData = json_decode($data);
		
		if($formData === null) return;
		
		$nick = $player->getName();
		
		$api = Main::getInstance()->getKitsAPI();
		
		
		  $diamond_helmet = Item::get(310, 0, 1);

 			$diamond_helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0), 4));
	 		$diamond_helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 3));
	 		
		  $diamond_chestplate = Item::get(311, 0, 1);

 			$diamond_chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0), 4));
	 		$diamond_chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 3));
	 		
		  $diamond_leggings = Item::get(312, 0, 1);

 			$diamond_leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0), 4));
	 		$diamond_leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 3));
	 		
		  $diamond_boots = Item::get(313, 0, 1);

 			$diamond_boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0), 4));
	 		$diamond_boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 3));
	 		
	 		$diamond_sword = Item::get(276, 0, 1);

			$diamond_sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(9), 5));
			$diamond_sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(13), 2));
			 
	 		 $diamond_sword_k = Item::get(276, 0, 1);

		 	 $diamond_sword_k->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(9), 5));
 			$diamond_sword_k->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(12), 2));
			 $diamond_pickaxe = Item::get(278, 0, 1);
			 
		 	$diamond_pickaxe->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), 5));
	 		$diamond_pickaxe->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 3));
	 	$bow = Item::get(261, 0, 1);

			$bow->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(19), 5));
			$bow->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(20), 2));
			$bow->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(22), 1));
			 
			 $arrow = Item::get(262);

            $rzucak = Item::get(46, 0, 2);
            $rzucak->setCustomName("§r§l§4Rzucane TNT");
            $rzucak->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));

            $kit_gracz = array_search("gracz", $this->kits);
            $kit_vip = array_search("vip", $this->kits);
            $kit_svip = array_search("svip", $this->kits);
            $kit_sponsor = array_search("sponsor", $this->kits);
            $kit_tnt = array_search("tnt", $this->kits);
            $kit_yt = array_search("yt", $this->kits);
            $kit_ytp = array_search("yt+", $this->kits);

		switch($formData) {
			
			//ZESTAW GRACZ
			case "{$kit_gracz}":
			 if($api->isCooldown($nick, "gracz")) {
			 	$player->sendMessage(Main::format("Nie mozesz teraz wziac tego kita!"));
			 	return;
			 }
			 
			 if(!$player->hasPermission("PolishHard.kits.cooldown"))
			 	$api->setCooldown($nick, "gracz", 60 * 10);
			 
			 $item1 = Item::get(257, 0, 1);


			$item1->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15), 5));
			$item1->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 3));
			
			 
			$item2 = Item::get(1, 0, 16);
			$item2->setCustomName("§r§7Generator Kamienia§4 3s");
	 	$item2->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 10));
			 
			$item3 = Item::get(17, 0, 16);
			$item4 = Item::get(364, 0, 64);
			
			$player->getInventory()->addItem($item1);
			$player->getInventory()->addItem($item2);
			$player->getInventory()->addItem($item3);
			$player->getInventory()->addItem($item4);
			 
			 $player->sendMessage("§8§l>§r §7Pomyslnie wybrano zestaw §4gracz§7!");
			break;
			
			//ZESTAW VIP
            case "{$kit_vip}":
			 if($api->isCooldown($nick, "vip")) {
			 	$player->sendMessage(Main::format("Nie mozesz teraz wziac tego kita!"));
			 	return;
			 }
			 
			 if(Main::getInstance()->startEdycji()) {
	 			$player->sendMessage(Main::format("Ten kit mozesz odebrac po godzinie §4".Main::getInstance()->getStartEdycjiTime()));
	 			return;
	 		}
			 
			 if(!$player->hasPermission("PolishHard.kits.cooldown"))
			 $api->setCooldown($nick, "vip", 60 * 360);
			 
			 $koxy = Item::get(466, 0, 4);
			 $refy = Item::get(322, 0, 10);
			 $perly = Item::get(368, 0, 6);
			 
			 $items = [$diamond_helmet, $diamond_chestplate, $diamond_leggings, $diamond_boots, $diamond_sword, $diamond_sword_k, $koxy, $refy, $perly, $diamond_pickaxe, $bow, $arrow];
			 
			 foreach($items as $item) {
			  if($player->getInventory()->canAddItem($item))
			   $player->getInventory()->addItem($item);
			  else
			   $player->getLevel()->dropItem($player->asVector3(), $item);
			 }
				
			 $player->sendMessage("§8§l>§r §7Pomyslnie wybrano zestaw §4VIP§7!");
			break;
			
			//ZESTAW SVIP
            case "{$kit_svip}":
			 if($api->isCooldown($nick, "svip")) {
			 	$player->sendMessage(Main::format("Nie mozesz teraz wziac tego kita!"));
			 	return;
			 }
			 
			 if(Main::getInstance()->startEdycji()) {
	 			$player->sendMessage(Main::format("Ten kit mozesz odebrac po godzinie §4".Main::getInstance()->getStartEdycjiTime()));
	 			return;
	 		}
			 
			 if(!$player->hasPermission("PolishHard.kits.cooldown"))
			 $api->setCooldown($nick, "svip", 60 * 720);
			 
			 $koxy = Item::get(466, 0, 10);
			 $refy = Item::get(322, 0, 20);
			 $perly = Item::get(368, 0, 14);
			 
			 $items = [$diamond_helmet, $diamond_chestplate, $diamond_leggings, $diamond_boots, $diamond_sword, $diamond_sword_k, $diamond_pickaxe, $bow];
			 
		 	$items_2 = [$koxy, $refy, $perly, $arrow];
			 
			 for($i = 0; $i <= 1; $i++) {
			  foreach($items as $item) {
			   if($player->getInventory()->canAddItem($item))
			    $player->getInventory()->addItem($item);
			   else
			    $player->getLevel()->dropItem($player->asVector3(), $item);
			  }
			 }
			 
			 foreach($items_2 as $item) {
			  if($player->getInventory()->canAddItem($item))
			   $player->getInventory()->addItem($item);
			  else
			   $player->getLevel()->dropItem($player->asVector3(), $item);
			 }
			 
			 $player->sendMessage("§8§l>§r §7Pomyslnie wybrano zestaw §4SVIP§7!");
			break;
			
			//ZESTAW SPONSOR
			case "{$kit_sponsor}":
			 if($api->isCooldown($nick, "sponsor")) {
			 	$player->sendMessage(Main::format("Nie mozesz teraz wziac tego kita!"));
			 	return;
			 }
			 
			 if(Main::getInstance()->startEdycji()) {
	 			$player->sendMessage(Main::format("Ten kit mozesz odebrac po godzinie §4".Main::getInstance()->getStartEdycjiTime()));
	 			return;
	 		}
			 
			 if(!$player->hasPermission("PolishHard.kits.cooldown"))
			 $api->setCooldown($nick, "sponsor", 60 * 1440);
		 
			 $koxy = Item::get(466, 0, 25);
			 $refy = Item::get(322, 0, 40);
			 $perly = Item::get(368, 0, 20);
			 
			 //to ma dac x3 a strzale ma dac oddzielnie bo ma byc tylko jedna
			 $items = [$diamond_helmet, $diamond_chestplate, $diamond_leggings, $diamond_boots, $diamond_sword, $diamond_sword_k, $diamond_pickaxe, $bow];
			 
			 $items_2 = [$koxy, $refy, $perly, $arrow];
		 
		 for($i = 0; $i <= 2; $i++) {
			  foreach($items as $item) {
			   if($player->getInventory()->canAddItem($item))
			    $player->getInventory()->addItem($item);
			   else
			    $player->getLevel()->dropItem($player->asVector3(), $item);
			  }
			 }
			 
			 foreach($items_2 as $item) {
			  if($player->getInventory()->canAddItem($item))
			   $player->getInventory()->addItem($item);
			  else
			   $player->getLevel()->dropItem($player->asVector3(), $item);
			 }
		 
			 $player->sendMessage("§8§l>§r §7Pomyslnie wybrano zestaw §4SPONSOR§7!");
			break;

            //ZESTAW TNT
            case "{$kit_tnt}":
                if($api->isCooldown($nick, "tnt")) {
                    $player->sendMessage(Main::format("Nie mozesz teraz wziac tego kita!"));
                    return;
                }

                if(Main::getInstance()->startEdycji()) {
                    $player->sendMessage(Main::format("Ten kit mozesz odebrac po godzinie §4".Main::getInstance()->getStartEdycjiTime()));
                    return;
                }

                if(!$player->hasPermission("PolishHard.kits.cooldown"))
                    $api->setCooldown($nick, "tnt", 60 * (1440*2));

                $items = [
                    Item::get(Item::TNT, 0, 64),
                    Item::get(Item::TNT, 0, 64),
                    $rzucak
                ];

                foreach($items as $item) {
                    if($player->getInventory()->canAddItem($item))
                        $player->getInventory()->addItem($item);
                    else
                        $player->getLevel()->dropItem($player->asVector3(), $item);
                }

                $player->sendMessage("§8§l>§r §7Pomyslnie wybrano zestaw §4TNT§7!");
                break;
			
			//ZESTAW YT
            case "{$kit_yt}":
			 if($api->isCooldown($nick, "yt")) {
			 	$player->sendMessage(Main::format("Nie mozesz teraz wziac tego kita!"));
			 	return;
			 }
			 
			 if(Main::getInstance()->startEdycji()) {
	 			$player->sendMessage(Main::format("Ten kit mozesz odebrac po godzinie §4".Main::getInstance()->getStartEdycjiTime()));
	 			return;
	 		}
			 
			 if(!$player->hasPermission("PolishHard.kits.cooldown"))
		   	 $api->setCooldown($nick, "yt", 60 * 240);
		   	 
			 $koxy = Item::get(466, 0, 2);
			 $refy = Item::get(322, 0, 8);
			 $perly = Item::get(368, 0, 4);
			 
			 //tu jak cos ma nie dawac strzaly
			 $items = [$diamond_helmet, $diamond_chestplate, $diamond_leggings, $diamond_boots, $diamond_sword, $koxy, $refy, $perly, $diamond_pickaxe];
			 
			 foreach($items as $item) {
			  if($player->getInventory()->canAddItem($item))
			   $player->getInventory()->addItem($item);
			  else
			   $player->getLevel()->dropItem($player->asVector3(), $item);
			 }
			 
			 $player->sendMessage("§8§l>§r §7Pomyslnie wybrano zestaw §4YT§7!");
			break;
			
			//ZESTAW YT+
			case "{$kit_ytp}":
			 if($api->isCooldown($nick, "yt+")) {
			 	$player->sendMessage(Main::format("Nie mozesz teraz wziac tego kita!"));
			 	return;
			 }
			 
			 if(Main::getInstance()->startEdycji()) {
	 			$player->sendMessage(Main::format("Ten kit mozesz odebrac po godzinie §4".Main::getInstance()->getStartEdycjiTime()));
	 			return;
	 		}
			 
			 if(!$player->hasPermission("PolishHard.kits.cooldown"))
			 $api->setCooldown($nick, "yt+", 60 * 480);
			 
			 $koxy = Item::get(466, 0, 4);
			 $refy = Item::get(322, 0, 10);
			 $perly = Item::get(368, 0, 6);
			 
		  //ma dac zbroje x2
			 $items = [$diamond_helmet, $diamond_chestplate, $diamond_leggings, $diamond_boots];
			 
			 $items_2 = [$diamond_sword, $diamond_sword_k, $koxy, $refy, $perly, $diamond_pickaxe];
			 
			 for($i = 0; $i <= 1; $i++) {
			  foreach($items as $item) {
			   if($player->getInventory()->canAddItem($item))
			    $player->getInventory()->addItem($item);
			   else
			    $player->getLevel()->dropItem($player->asVector3(), $item);
			  }
			 }
			 
			 foreach($items_2 as $item) {
			  if($player->getInventory()->canAddItem($item))
			   $player->getInventory()->addItem($item);
			  else
			   $player->getLevel()->dropItem($player->asVector3(), $item);
			 }
			 
			 $player->sendMessage("§8§l>§r §7Pomyslnie wybrano zestaw §4YT+");
			break;
		}
		
		$player->sendForm(new KitsForm($player));
	}
}