<?php
namespace mart\drop;
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
		$this->getServer()->getLogger()->info(TextFormat::GREEN."[db] Włączony!");
	}
	
	public function onBreakssss(BlockBreakEvent $ev){
  	if(!($ev->isCancelled())){

  $player = $ev->getPlayer();

  if($ev->getBlock()->getId() == 1){

  $player->addExperience(10);

  }

}
		}
	
	public function onBreak(BlockBreakEvent $e){
		$player = $e->getPlayer();
		$block = $e->getBlock();
		$gracz = $e->getPlayer()->getName();
		if($e->getBlock()->getId() == 1){
			 switch(mt_rand(1,120)){
         case 1:
         $player->sendTip("§7• §7Trafiles na §6Emerald §8(§bx1§8)");
         $player->getInventory()->addItem(Item::get(388, 0, 1));
		    
         break;
         case 2:
         $player->sendTip("§7• §7Trafiles na §fLapizu §8(§bx1§8)§7");
         $player->getInventory()->addItem(Item::get(351, 4, 1));
		 
         break;
		 case 3:
         $player->sendTip("§7• §7Trafiles na §6Diament §8(§bx3§8)");
         $player->getInventory()->addItem(Item::get(264,0, 3));
		 
		 break;
		 case 4:
         $player->sendTip("§7• §7Trafiles na §6Biblioteczek §8(§bx1§8)§7");
         $player->getInventory()->addItem(Item::get(47,0, 1));
		 
		 break;
		 case 5:
         $player->sendTip("§7• §7Trafiles na §eObsydianu §8(§bx5§8)§7");
         $player->getInventory()->addItem(Item::get(49,0, 5));
		 
		 break;
		 case 6:
         $player->sendTip("§7• §7Trafiles na §6Sztabki Złota §8(§bx7§8)");
         $player->getInventory()->addItem(Item::get(266,0, 7));
		 
		 break;
		 case 7:
         $player->sendTip("§7• §7Trafiles na §fSztabke Żelaza §8(§bx1§8) ");
         $player->getInventory()->addItem(Item::get(265,0, 1));
		 
		 break;
		 case 8:
         $player->sendTip("§7• §7Trafiles na §6Diamenty  §8(§bx3§8)");
         $player->getInventory()->addItem(Item::get(264,0, 3));
		 
		 break;
		 case 9:
         $player->sendTip("§7• §7Trafiles na §eObsydianu §8(§bx3§8)");
         $player->getInventory()->addItem(Item::get(49,0, 3));
		 
		 break;
		 case 10:
         $player->sendTip("§7• §7Trafiles na §cJabłka §8(§bx3§8)");
         $player->getInventory()->addItem(Item::get(260,0, 3));
		 
		 break;
		 case 11:
         $player->sendTip("§7• §7Trafiles na §ePerla §8(§bx1§8)");
         $player->getInventory()->addItem(Item::get(332,0, 1));
		 
         break;
		 case 12:
         $player->sendTip("§7• §7Trafiles na §fSztabek Żelaza §8(§bx5§8)");
         $player->getInventory()->addItem(Item::get(265,0, 5));
		 
         break;
		 case 13:
         $player->sendTip("§7• §7Trafiles na §6Sztabki Złota §8(§bx4§8)");
         $player->getInventory()->addItem(Item::get(266,0, 4));
		 
		 break;
}
		}
	}
}