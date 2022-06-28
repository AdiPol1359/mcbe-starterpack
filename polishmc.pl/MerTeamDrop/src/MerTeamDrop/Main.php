<?php

namespace MerTeamDrop;

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
			 switch(mt_rand(1,60)){
		    case 1:
		    $player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aDiamenty §7w ilości: §a2 ");
		    $player->getInventory()->addItem(Item::get(264, 0, 3));
		    break;
			case 2:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aDiamenty §7w ilości: §a3 ");
			$player->getInventory()->addItem(Item::get(264, 0, 3));
			break;
			case 3:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aDiamenty §7w ilości: §a5 ");
			$player->getInventory()->addItem(Item::get(264, 0, 5));
			break;
			case 4:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aZłoto §7w ilości: §a5 ");
			$player->getInventory()->addItem(Item::get(266, 0, 5));
			break;
			case 5:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aZłoto §7w ilości: §a2 ");
			$player->getInventory()->addItem(Item::get(266, 0, 2));
			break;
			case 6:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aZłoto §7w ilości: §a1 ");
			$player->getInventory()->addItem(Item::get(266, 0, 1));
			break;
			case 7:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aŻelazo §7w ilości: §a6 ");
			$player->getInventory()->addItem(Item::get(265, 0, 6));
			break;
			case 8:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aCoins §7w ilości: §a2 ");
			$player->getInventory()->addItem(Item::get(341, 0, 2));
			break;
			case 9:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aŻelazo §7w ilości: §a2 ");
			$player->getInventory()->addItem(Item::get(265, 0, 2));
			break;
			case 10:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aŻelazo §7w ilości: §a5 ");
			$player->getInventory()->addItem(Item::get(265, 0, 5));
			break;
			case 11:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aEmeraldy §7w ilości: §a4 ");
			$player->getInventory()->addItem(Item::get(388, 0, 4));
			break;
			case 12:
		    $player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aEmeraldy §7w ilości: §a5 ");
			$player->getInventory()->addItem(Item::get(388, 0, 5));
			break;
			case 13:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aEmeraldy §7w ilości: §a1 ");
			$player->getInventory()->addItem(Item::get(388, 0, 1));
			break;
			case 14:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aObsydian §7w ilości: §a5 ");
			$player->getInventory()->addItem(Item::get(49, 0, 5));
			break;
			case 15:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aObsydian §7w ilości: §a3 ");
			$player->getInventory()->addItem(Item::get(49, 0, 3));
			break;
			case 16:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aWęgiel §7w ilości: §a3 ");
			$player->getInventory()->addItem(Item::get(263, 0, 3));
			break;
			case 17:
			$player->sendTip("§8• (§aDROP§8) §7Wykopałeś: §aTnT §7w ilości: §a3 ");
			$player->getInventory()->addItem(Item::get(46, 0, 3));
			break;
			 }
		}
	}
}
