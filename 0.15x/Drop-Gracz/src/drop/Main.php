<?php

namespace drop;

use pocketmine\plugin\PluginBase as P;
use pocketmine\event\Listener as L;
use pocketmine\utils\TextFormat;
use pocketmine\utils\MainLogger;
use pocketmine\block\Air;
use pocketmine\entity\Effect;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\level\sound\PopSound as Pop;
use pocketmine\level\particle\LavaParticle as Lava;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\ConsoleCommandSender;

class Main extends P implements L{
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->saveDefaultConfig();
		$this->getServer()->getLogger()->info(TextFormat::GREEN."[DROP] Włączony!");
	}

	
	public function onBreak(BlockBreakEvent $e){
		$player = $e->getPlayer();
		$block = $e->getBlock();
		$gracz = $e->getPlayer()->getName();
		if($e->getBlock()->getId() == 1){
			 switch(mt_rand(1,90)){
		 case 1:
         $player->sendTip("§c[§e+§c] §d Perla §cszt. §e1");
         $player->getInventory()->addItem(Item::get(332, 0, 1));
		 $player->addExperience(6);
         case 2:
         $player->sendTip("§c[§e+§c] §6 Zloto §cszt. §e3");
         $player->getInventory()->addItem(Item::get(266, 0, 3));
		 $player->addExperience(4);
         break;
         case 3:
         $player->sendTip("§c[§e+§c] §6 Zloto §cszt. §e2");
         $player->getInventory()->addItem(Item::get(266, 0, 2));
		 $player->addExperience(3);
         break;
         case 4:
         $player->sendTip("§c[§e+§c] §6 Zloto §cszt. §e1");
         $player->getInventory()->addItem(Item::get(266, 0, 1));
		 $player->addExperience(2);
         break;
		 		 case 5:
		 $player->sendTip("§c[§e+§c] §b Diament §cszt. §e3");
		 $player->getInventory()->addItem(Item::get(264, 0, 3));
		 		 $player->addExperience(3);
		 break;
		 		 case 6:
		 $player->sendTip("§c[§e+§c] §b Diament §cszt. §e2");
		 $player->getInventory()->addItem(Item::get(264, 0, 2));
		 		 $player->addExperience(2);
		 break;
		 		 case 7:
		 $player->sendTip("§c[§e+§c] §b Diament §cszt. §e1");
		 $player->getInventory()->addItem(Item::get(264, 0, 1));
		 		 $player->addExperience(1);
		 break;
		          case 8:
         $player->sendTip("§c[§e+§c] §0 Obsydian §cszt. §e3");
         $player->getInventory()->addItem(Item::get(49, 0, 3));
		 $player->addExperience(2);
         break;
		          case 9:
         $player->sendTip("§c[§e+§c] §0 Obsydian §cszt. §e2");
         $player->getInventory()->addItem(Item::get(49, 0, 2));
		 $player->addExperience(2);
         break;
		          case 10:
         $player->sendTip("§c[§e+§c] §0 Obsydian §cszt. §e1");
         $player->getInventory()->addItem(Item::get(49, 0, 1));
		 $player->addExperience(2);
         break;
		          case 11:
         $player->sendTip("§c[§e+§c] §a Emerald §cszt. §e3");
         $player->getInventory()->addItem(Item::get(388, 0, 3));
		 $player->addExperience(2);
         break;
		          case 12:
         $player->sendTip("§c[§e+§c] §a Emerald §cszt. §e2");
         $player->getInventory()->addItem(Item::get(388, 0, 2));
		 $player->addExperience(2);
         break;
		          case 13:
         $player->sendTip("§c[§e+§c] §a Emerald §cszt. §e1");
         $player->getInventory()->addItem(Item::get(388, 0, 1));
		 $player->addExperience(2);
         break;
		 		 case 14:
         $player->sendTip("§c[§e+§c] §f Zelazo §cszt. §e3");
         $player->getInventory()->addItem(Item::get(15, 0, 3));
		 $player->addExperience(2);
         break;
		 		 case 15:
         $player->sendTip("§c[§e+§c] §f Zelazo §cszt. §e2");
         $player->getInventory()->addItem(Item::get(15, 0, 2));
		 $player->addExperience(2);
         break;
		 		 case 16:
         $player->sendTip("§c[§e+§c] §f Zelazo §cszt. §e1");
         $player->getInventory()->addItem(Item::get(15, 0, 1));
		 $player->addExperience(2);
         break;
		 		          case 17:
         $player->sendTip("§c[§e+§c] §7 Proszek §cszt. §e3");
         $player->getInventory()->addItem(Item::get(289, 0, 3));
		 $player->addExperience(3);
         break;
		 		          case 18:
         $player->sendTip("§c[§e+§c] §7 Proszek §cszt. §e2");
         $player->getInventory()->addItem(Item::get(289, 0, 2));
		 $player->addExperience(2);
         break;
		 		          case 19:
         $player->sendTip("§c[§e+§c] §7 Proszek §cszt. §e1");
         $player->getInventory()->addItem(Item::get(289, 0, 1));
		 $player->addExperience(1);
         break;
		 		 case 20:
         $player->sendTip("§c[§e+§c] §3 Ksiazki §cszt. §e3");
         $player->getInventory()->addItem(Item::get(340, 0, 3));
		 $player->addExperience(1);
         break;
		 		 case 21:
         $player->sendTip("§c[§e+§c] §3 Ksiazki §cszt. §e2");
         $player->getInventory()->addItem(Item::get(340, 0, 2));
		 $player->addExperience(1);
         break;
		 		 case 22:
         $player->sendTip("§c[§e+§c] §3 Ksiazki §cszt. §e1");
         $player->getInventory()->addItem(Item::get(340, 0, 1));
		 $player->addExperience(1);
         break;
		 		 		 case 23:
         $player->sendTip("§c[§e+§c] §9 Lapis §cszt. §e3");
         $player->getInventory()->addItem(Item::get(351, 4, 3));
		 $player->addExperience(1);
         break;
		 		 		 case 24:
         $player->sendTip("§c[§e+§c] §9 Lapis §cszt. §e2");
         $player->getInventory()->addItem(Item::get(340, 0, 2));
		 $player->addExperience(1);
         break;
		 		 		 case 25:
         $player->sendTip("§c[§e+§c] §9 Lapis §cszt. §e1");
         $player->getInventory()->addItem(Item::get(340, 0, 1));
		 $player->addExperience(1);
         break;
         case 26:
         $player->sendTip("§c[§e+§c] §8 Wegiel §cszt. §e3");
         $player->getInventory()->addItem(Item::get(263, 0, 3));
		 $player->addExperience(1);
         break;
		          case 27:
         $player->sendTip("§c[§e+§c] §8 Wegiel §cszt. §e2");
         $player->getInventory()->addItem(Item::get(263, 0, 2));
		 $player->addExperience(1);
         break;
		          case 28:
         $player->sendTip("§c[§e+§c] §8 Wegiel §cszt. §e1");
         $player->getInventory()->addItem(Item::get(263, 0, 1));
		 $player->addExperience(1);
         break;
			 }
		}
		}
	}
