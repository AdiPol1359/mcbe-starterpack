<?php

namespace Dirtex;

use pocketmine\plugin\PluginBase as P;
use pocketmine\event\Listener as L;
use pocketmine\utils\TextFormat;
use pocketmine\utils\MainLogger;
use pocketmine\block\Air;
use pocketmine\Server;
use pocketmine\event\block\BlockPlaceEvent as BPL;
use pocketmine\level\sound\PopSound as Pop;
use pocketmine\level\particle\LavaParticle as Lava;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\item\Item;

class Main extends P implements L{
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->saveDefaultConfig();
		$this->getServer()->getLogger()->info(TextFormat::GREEN."[COBBLEX Nowy!] Włączony!");
	}

	
	public function onPlace(BPL $e){
		$player = $e->getPlayer();
		$block = $e->getBlock();
		$gracz = $e->getPlayer()->getName();
		if($e->getBlock()->getId() == 129){
			 switch(mt_rand(1,20)){
         case 1:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(1) §7Mikstura Niewidzialności 3:00 §f•");
         $player->getInventory()->addItem(Item::get(373, 7, 1));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 2:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(1) §7Mikstura Regeneracji 2:00 §f•");
         $player->getInventory()->addItem(Item::get(373, 29, 1));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 3:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(2) §7Mikstura Siły §f•");
         $player->getInventory()->addItem(Item::get(373, 32, 2));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 4:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(1) §7CobbleX §f•");
         $player->getInventory()->addItem(Item::get(129, 0, 1));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 5:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(15) §7Diamentów §f•");
         $player->getInventory()->addItem(Item::get(264, 0, 15));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 6:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(10) §7Melon §f•");
         $player->getInventory()->addItem(Item::get(360, 0, 10));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 7:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c() §7Złote jabko (Koxa) §f•");
         $player->getInventory()->addItem(Item::get(466, 0, 1));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 8:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(30) §7Kamieni §f•");
         $player->getInventory()->addItem(Item::get(1, 0, 30));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 9:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(13) §7Jabłek §f•");
         $player->getInventory()->addItem(Item::get(260, 0, 13));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 10:
         $this->getServer()->broadcastMessage("§f• §8[§eCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(15) §7Żelazo §f•");
         $player->getInventory()->addItem(Item::get(265, 0, 15));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 11:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(17) §7Złota §f•");
         $player->getInventory()->addItem(Item::get(266, 0, 17));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 12:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(47) §7Piasek §f•");
         $player->getInventory()->addItem(Item::get(12, 0, 47));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 13:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(64) §7Szkła §f•");
         $player->getInventory()->addItem(Item::get(20, 0, 64));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 14:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(1) §7Stół do enchantowania §f•");
         $player->getInventory()->addItem(Item::get(116, 0, 1));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 15:
         $this->getServer()->broadcastMessage("§§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(1) §7Statyw alchemiczny §f•");
         $player->getInventory()->addItem(Item::get(379, 0, 1));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 16:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(38) §7TNT §f•");
         $player->getInventory()->addItem(Item::get(46, 0, 38));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 17:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(34) §7Półki z książkami §f•");
         $player->getInventory()->addItem(Item::get(47, 0, 34));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 18:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(37) §7Obsidian §f•");
         $player->getInventory()->addItem(Item::get(49, 0, 37));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 19:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7wylosował: §c(4) §7Stoniarki §f•");
         $player->getInventory()->addItem(Item::get(121, 0, 4));
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();
         break;
         case 20:
         $this->getServer()->broadcastMessage("§f• §8[§cCobbleX§eHC§8] §7Gracz §c$gracz §7nic nie wylosował §f•");
         $player->getInventory()->addItem(Item::get());
         $player->addExperience(1);
         $player->getInventory()->removeItem(Item::get(129, 0, 1));
         $e->setCancelled();

}
		}
	}
}