<?php

namespace PolishMC_Kity;

use pocketmine\plugin\PluginBase;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

use pocketmine\inventory\Inventory;

use pocketmine\scheduler\Task;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

class Main extends PluginBase{
	
	public function f(String $w){
		return "§8• [§cGRAYHC§8] §7$w §8•";
	}
	
	public function onEnable(){
		
		$this->getScheduler()->scheduleDelayedRepeatingTask(new KityTask($this), 20*60, 20*60);
		
		$this->db = new \SQLite3($this->getDataFolder() . "DataBase.db");
		$this->db->exec("CREATE TABLE IF NOT EXISTS kity (nick TEXT, kit TEXT, czas INT)");
		
		$this->getLogger()->info("Plugin włączono");
	}
	public function onDisable(){
		$this->getLogger()->info("Plugin wyłączono");
	}
	  public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args) : bool {
	 if($cmd->getName() == "kit"){
	  			$this->Kity($sender);
	return true;
	     
	 }
	}
	
	public function UstawCooldown(String $nick, String $kit, int $czas){
		
		$save = $this->db->prepare("INSERT OR REPLACE INTO kity (nick, kit, czas) VALUES (:nick, :kit, :czas)");
							$save->bindValue(":nick", $nick);
							$save->bindValue(":kit", $kit);
							$save->bindValue(":czas", $czas);
							$save->execute();
		
	}
	
	public function CzyCooldown(String $nick, String $kit){
		
  	$result = 	$this->db->query("SELECT * FROM kity WHERE nick = '$nick' AND kit = '$kit'");
	  $array = $result->fetchArray(SQLITE3_ASSOC);
	  
	  return empty($array) == false;
		
	}
	
	public function Cooldown(String $nick, String $kit){
		
  	$result = 	$this->db->query("SELECT * FROM kity WHERE nick = '$nick' AND kit = '$kit'");
	  $array = $result->fetchArray(SQLITE3_ASSOC);
	  
	  $minuty = $array["czas"];
		
		if($minuty < 60){
        	
        	$f_minuty = "minut";
        	
        	if($minuty == 1){
        		$f_minuty = "minute";
        	}
        	
        	if($minuty > 1 && $minuty < 5){
        		$f_minuty = "minuty";
        	}
        	
            return $this->f("Kit §c$kit §7bedzie dostepny za §c$minuty §7$f_minuty");
        }
        
        	if(!($minuty % 60 == 0)){
        	$godziny = floor($minuty / 60);
        	$minuty = $minuty % 60;
        	
        	$f_minuty = "minut";
        	
        	if($minuty == 1){
        		$f_minuty = "minute";
        	}
        	
        	if($minuty > 1 && $minuty < 5){
        		$f_minuty = "minuty";
        	}
        	
        	$f_godziny = "godzin";
        	
        	if($godziny == 1){
        		$f_godziny = "godzine";
        	}
        	
        	if($godziny > 1 && $godziny < 5){
        		$f_godziny = "godziny";
        	}
        	
           return $this->f("Kit §c$kit §7bedzie dostepny za §c$godziny §7$f_godziny i §c$minuty §7$f_minuty");
        }
		
	}
	

  public function Kity(Player $gracz){
 	 
 	 $nick = $gracz->getName();
 	 
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $gracz, int $rezultat = null){
			
			$nick = $gracz->getName();
 	 
 	 //enchanty
	  		 	$prot_4 = new EnchantmentInstance(Enchantment::getEnchantment(0), 4);
	  		 	$unb_3 = new EnchantmentInstance(Enchantment::getEnchantment(17), 3);
	  		 	$sharp_5 = new EnchantmentInstance(Enchantment::getEnchantment(9), 5);
	  		 		$fire_aspect_2 = new EnchantmentInstance(Enchantment::getEnchantment(13), 2);
	  		 			$eff_5 = new EnchantmentInstance(Enchantment::getEnchantment(15), 5);
	  		 			
	  		 			$knockback_2 = new EnchantmentInstance(Enchantment::getEnchantment(12), 2);
	  		 			
	  		 			//itemy
	  		 			$helm = Item::get(310, 0, 1);
	  		 			$helm->addEnchantment($prot_4);
	  		 			$helm->addEnchantment($unb_3);
	  		 			
	  		 			$klata = Item::get(311, 0, 1);
	  		 			$klata->addEnchantment($prot_4);
	  		 			$klata->addEnchantment($unb_3);
	  		 			
	  		 			$spodnie = Item::get(312, 0, 1);
	  		 			$spodnie->addEnchantment($prot_4);
	  		 			$spodnie->addEnchantment($unb_3);
	  		 			
	  		 			$buty = Item::get(313, 0, 1);
	  		 			$buty->addEnchantment($prot_4);
	  		 			$buty->addEnchantment($unb_3);
	  		 			
	  		 		 $miecz_sharp_fire = Item::get(276, 0, 1);
	  		 			$miecz_sharp_fire->addEnchantment($sharp_5);
	  		 			$miecz_sharp_fire->addEnchantment($fire_aspect_2);
	  		 			
	  		 			$miecz_sharp_knock = Item::get(276, 0, 1);
	  		 			$miecz_sharp_knock->addEnchantment($sharp_5);
	  		 			$miecz_sharp_knock->addEnchantment($knockback_2);
	  		 			
	  		 			$kilof = Item::get(278, 0, 1);
	  		 			$kilof->addEnchantment($eff_5);
	  		 			$kilof->addEnchantment($unb_3);
 	 
				 switch($rezultat){
					case "0":
     //KIT GRACZ
     if($this->CzyCooldown($nick, "Gracz")){
	  					$cooldown = $this->Cooldown($nick, "Gracz");
	  					$gracz->sendMessage($cooldown);
	  					return true;
	  				}
	  				$cooldown = 10;
	  				$this->UstawCooldown($nick, "Gracz", $cooldown);
	  				$gracz->sendMessage($this->f("Pomyslnie wybrano kit §cGracz"));
     $gracz->getInventory()->addItem(Item::get(274, 0, 1));
	  				$gracz->getInventory()->addItem(Item::get(364, 0, 32));
     
     $this->Kity($gracz);
     
					break;
					case "1":
     //KIT VIP
     if($this->CzyCooldown($nick, "VIP")){
	  					$cooldown = $this->Cooldown($nick, "VIP");
	  					$gracz->sendMessage($cooldown);
	  					return true;
	  				}
	  				$godziny = 4;
	  				$minuty = 30;
	  				$cooldown = $godziny*60 + $minuty;
	  				$this->UstawCooldown($nick, "VIP", $cooldown);
	  				$gracz->sendMessage($this->f("Pomyslnie wybrano kit §cVIP"));
	  				
     $gracz->getArmorInventory()->setHelmet($helm);
			$gracz->getArmorInventory()->setChestplate($klata);
			$gracz->getArmorInventory()->setLeggings($spodnie);
   $gracz->getArmorInventory()->setBoots($buty);
   
   $gracz->getInventory()->addItem($miecz_sharp_fire);
   $gracz->getInventory()->addItem(Item::get(466, 0, 1));
   $gracz->getInventory()->addItem(Item::get(322, 0, 2));
   $gracz->getInventory()->addItem($kilof);
     
     $this->Kity($gracz);
					break;
					case "2":
     //KIT SVIP
     if($this->CzyCooldown($nick, "SVIP")){
	  					$cooldown = $this->Cooldown($nick, "SVIP");
	  					$gracz->sendMessage($cooldown);
	  					return true;
	  				}
	  				$godziny = 5;
	  				$minuty = 30;
	  				$cooldown = $godziny*60 + $minuty;
	  				$this->UstawCooldown($nick, "SVIP", $cooldown);
	  				$gracz->sendMessage($this->f("Pomyslnie wybrano kit §cSVIP"));
	  				
     $gracz->getArmorInventory()->setHelmet($helm);
			$gracz->getArmorInventory()->setChestplate($klata);
			$gracz->getArmorInventory()->setLeggings($spodnie);
   $gracz->getArmorInventory()->setBoots($buty);
   
   $gracz->getInventory()->addItem($miecz_sharp_fire);
   $gracz->getInventory()->addItem($miecz_sharp_knock);
   $gracz->getInventory()->addItem(Item::get(466, 0, 3));
   $gracz->getInventory()->addItem(Item::get(322, 0, 5));
	  $gracz->getInventory()->addItem($kilof);
     
     $this->Kity($gracz);
					break;
					
					case "3":
     //KIT SPONSOR
     if($this->CzyCooldown($nick, "SPONSOR")){
	  					$cooldown = $this->Cooldown($nick, "SPONSOR");
	  					$gracz->sendMessage($cooldown);
	  					return true;
	  				}
	  				$godziny = 6;
	  				$minuty = 30;
	  				$cooldown = $godziny*60 + $minuty;
	  				$this->UstawCooldown($nick, "SPONSOR", $cooldown);
	  				$gracz->sendMessage($this->f("Pomyslnie wybrano kit §cSPONSOR"));
	  				
     $gracz->getArmorInventory()->setHelmet($helm);
			$gracz->getArmorInventory()->setChestplate($klata);
			$gracz->getArmorInventory()->setLeggings($spodnie);
   $gracz->getArmorInventory()->setBoots($buty);
   
   $gracz->getInventory()->addItem($miecz_sharp_fire);
   $gracz->getInventory()->addItem($miecz_sharp_knock);
   $gracz->getInventory()->addItem(Item::get(466, 0, 5));
   $gracz->getInventory()->addItem(Item::get(322, 0, 7));
	  $gracz->getInventory()->addItem($kilof);
     
     $this->Kity($gracz);
					break;
				}
			});
			$form->setTitle("§l§ePOLISHMC.PL - Kity");
			
			$form->addButton($this->CzyCooldown($nick, "Gracz") ? "§cKit Gracz" : "§2Kit Gracz");
			
			if($gracz->hasPermission("kit.vip")){
				$form->addButton($this->CzyCooldown($nick, "VIP") ? "§cKit VIP" : "§2Kit VIP");
			}
			
			if($gracz->hasPermission("kit.svip")){
				$form->addButton($this->CzyCooldown($nick, "SVIP") ? "§cKit SVIP" : "§2Kit SVIP");
			}
			
			if($gracz->hasPermission("kit.sponsor")){
				$form->addButton($this->CzyCooldown($nick, "SPONSOR") ? "§cKit SPONSOR" : "§2Kit SPONSOR");
			}
			
			$form->sendToPlayer($gracz);
			return $form;
	}
  }
	
	
class KityTask extends Task{
	

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function onRun($tick){
    	
    	$result = $this->plugin->db->query("SELECT * FROM kity");
    	
    	while($array = $result->fetchArray(SQLITE3_ASSOC)){
    	
    	$nick = $array["nick"];
    	$kit = $array["kit"];
    	$czas = $array["czas"];
    	
    	if($czas <= 1){
    		$this->plugin->db->query("DELETE FROM kity WHERE nick = '$nick' AND kit = '$kit'");
    	}
    	
    	$this->plugin->db->query("UPDATE kity SET czas = czas - '1' WHERE nick='$nick'");
    	
  }
  }
}