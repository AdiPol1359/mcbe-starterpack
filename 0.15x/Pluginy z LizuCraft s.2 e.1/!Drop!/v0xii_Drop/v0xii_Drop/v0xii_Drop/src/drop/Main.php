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

class Main extends P implements L{
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->saveDefaultConfig();
		$this->getServer()->getLogger()->info(TextFormat::GREEN."[DropStone] Włączony!");
	}

	
	public function onBreak(BlockBreakEvent $e){
		$player = $e->getPlayer();
		$block = $e->getBlock();
		$gracz = $e->getPlayer()->getName();
		if($e->getBlock()->getId() == 1){
			 switch(mt_rand(1,200)){
		    case 1:
		    $player->sendMessage("§7• >Trafiłeś na: §c(3) §bDiamenty §a(+5exp)");
		    $player->getInventory()->addItem(Item::get(264, 0, 3));
		    break;
         case 2:
         $player->sendMessage("§7• >Trafiłeś na: §c(1) §bDiament §a(+5exp)");
         $player->getInventory()->addItem(Item::get(264, 0, 1));
         break;
         case 3:
         $player->sendMessage("§7• >Trafiłeś na: §c(1) §7Żelazo §a(+3exp)");
         $player->getInventory()->addItem(Item::get(265, 0, 1));
         break;
         case 4:
         $player->sendMessage("§7• >Trafiłeś na: §c(1) §eZłoto §a(+4exp)");
         $player->getInventory()->addItem(Item::get(266, 0, 1));
         break;
         case 8:
         $player->sendMessage("§7• >Trafiłeś na: §c(4) §dObsidian §a(+2exp)");
         $player->getInventory()->addItem(Item::get(49, 0, 4));
         break;
         case 5:
         $player->sendMessage("§7• >Trafiłeś na: §c(1) §1Butelke exp'a §a(+2exp)");
         $player->getInventory()->addItem(Item::get(384, 0, 1));
         break;
         case 7:
         $player->sendMessage("§7• >Trafiłeś na: §c(1) §aEmerald §a(+3exp)");
         $player->getInventory()->addItem(Item::get(388, 0, 1));
         break;
         case 8:
         $player->sendMessage("§7• >Trafiłeś na: §c(5) §4Redstone §a(+2exp)");
         $player->getInventory()->addItem(Item::get(331, 0, 5));
         break;
         case 9:
         $player->sendMessage("§7• >Trafiłeś na: §6Szybsze kopanie §a(+4exp)");
         $effect = Effect::getEffect(3);
         $effect->setDuration(600);
         $player->addEffect($effect);
         $player->addExperience(1);
         break;
         case 10:
         $player->sendMessage("§7• >Trafiłeś na: §c(6) §cJabłek §a(+2exp)");
         $player->getInventory()->addItem(Item::get(260, 0, 6));
         break;
		 case 11:
         $player->sendMessage("§7• >Trafiłeś na: §c(5) §0Węgla §a(+1exp)");
         $player->getInventory()->addItem(Item::get(263, 0, 5));
         break;
		 case 12:
         $player->sendMessage("§7• >Trafiłeś na: §c(2) §0Węgla §a(+1exp)");
         $player->getInventory()->addItem(Item::get(263, 0, 2));
         break;
		 case 13:
         $player->sendMessage("§7• >Trafiłeś na: §c(6) §7Żelaza §a(+3exp)");
         $player->getInventory()->addItem(Item::get(265, 0, 6));
         break;
		 case 14:
         $player->sendMessage("§7• >Trafiłeś na: §c(4) §eZłota §a(+4exp)");
         $player->getInventory()->addItem(Item::get(266, 0, 4));
         break;
		 case 15:
         $player->sendMessage("§7• >Trafiłeś na: §c(9) §9Lapis Lazuli §a(+1exp)");
         $player->getInventory()->addItem(Item::get(351, 4, 9));
         break;
		 case 16:
		 $player->sendMessage("§7• >Trafiłeś na: §c(2) §bDiamenty §a(+5exp)");
		 $player->getInventory()->addItem(Item::get(264, 0, 2));
		 break;
			 }
		}
	}
}
