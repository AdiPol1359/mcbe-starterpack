<?php

namespace xStrixU;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\{CommandSender, Command};
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\inventory\Inventory;
use pocketmine\{Server, Player};

class Main extends PluginBase implements Listener {
			
	public function onEnable() {
		
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->notice("Schowek Ładowanie...");
		@mkdir($this->getDataFolder());
	}
	//txt
	public function onJoin(PlayerJoinEvent $event) {
		$playerName = $event->getPlayer()->getName();
		if(!file_exists($this->getDataFolder().$playerName."_koxy.txt")) {
			file_put_contents($this->getDataFolder().$playerName."_koxy.txt", "0");
		}
		if(!file_exists($this->getDataFolder().$playerName."_refile.txt")) {
			file_put_contents($this->getDataFolder().$playerName."_refile.txt", "0");
		}
		if(!file_exists($this->getDataFolder().$playerName."_perly.txt")) {
			file_put_contents($this->getDataFolder().$playerName."_perly.txt", "0");
		}
	}
	public function onHeld(PlayerItemHeldEvent $event) {
		//kox
		if($event->getItem()->getId() == 466 && $event->getItem()->getCount() >= 3) {
			$event->getPlayer()->getInventory()->removeItem(Item::get(466, 1, $event->getItem()->getCount() - 2));
			file_put_contents($this->getDataFolder() .$event->getPlayer()->getName()."_koxy.txt", file_get_contents($this->getDataFolder().$event->getPlayer()->getName()."_koxy.txt")+$event->getItem()->getCount() - 2);
			$event->getPlayer()->sendMessage("§8• §7Osiagnales limit koxow w ekwipunku! §8•");
			$event->getPlayer()->sendMessage("§8• §7Nadmiar koxow zostal przeniesiony do schowka! §8•");
		}
		//refil
		if($event->getItem()->getId() == 322 && $event->getItem()->getCount() >= 9) {
			$event->getPlayer()->getInventory()->removeItem(Item::get(322, 0, $event->getItem()->getCount() - 8));
			file_put_contents($this->getDataFolder() .$event->getPlayer()->getName()."_refile.txt", file_get_contents($this->getDataFolder().$event->getPlayer()->getName()."_refile.txt")+$event->getItem()->getCount() - 8);
			$event->getPlayer()->sendMessage("§8• §7Osiagnales limit refilow w ekwipunku! §8•");
			$event->getPlayer()->sendMessage("§8• §7Nadmiar refilow zostal przeniesiony do schowka! §8•");
		}
		//perla jako jajko
		if($event->getItem() ->getId() == 332 && $event->getItem()->getCount() >= 5) {
			$event->getPlayer()->getInventory()->removeItem(Item::get(332, 0, $event->getItem()->getCount() - 4));
			file_put_contents($this->getDataFolder() .$event->getPlayer()->getName()."_perly.txt", file_get_contents($this->getDataFolder().$event->getPlayer()->getName()."_perly.txt")+$event->getItem()->getCount() - 4);
			$event->getPlayer()->sendMessage("§8• §7Osiagnales limit perel w ekwipunku! §8•");
			$event->getPlayer()->sendMessage("§8• §7Nadmiar perel zostal przeniesiony do schowka! §8•");
		}
	}
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		if($command->getName() == "schowek") {
			if(!isset($args[0])) {				
				$sender->sendMessage("§8• §a/schowek stan §7- Pokazuje aktualny stan schowka §8•");
				$sender->sendMessage("§8• §a/schowek §7<§awyplac§7> <§akoxy§7/§arefy§7/§aperly§7> §7<§ailosc§7> §8•");                               	
				$sender->sendMessage("§8• §a/schowek §7<§awplac§7> <§akoxy§7/§arefy§7/§aperly§7> §7<§ailosc§7> §8•");  	    
				return true;
			}
			if($args[0] == "wyplac" && $args[1] == "koxy") {
				if(is_numeric($args[2])) {
					if($args[2] <= file_get_contents($this->getDataFolder().$sender->getPlayer()->getName()."_koxy.txt")) {
						file_put_contents($this->getDataFolder().$sender->getPlayer()->getName()."_koxy.txt", file_get_contents($this->getDataFolder().$sender->getPlayer()->getName()."_koxy.txt")-$args[2]);
						$sender->getInventory()->addItem(Item::get(466, 1, $args[2]));
						$sender->sendMessage("§8• §7Pomyslnie wyplaciles §a$args[2] §7koxow ze schowka! §8•");
					} else {
						$sender->sendMessage("§8• §7Nie posiadasz takiej ilosci koxow aby wyplacic! §8•");
						return false;
					}
				} else {
					$sender->sendMessage("§8•§7 poprawne uzycie: /schowek wyplac koxy <ilosc> §8•");
				}
			}
			if($args[0] == "wyplac" && $args[1] == "refy") {
				if(is_numeric($args[2])) {
					if($args[2] <= file_get_contents($this->getDataFolder().$sender->getPlayer()->getName()."_refile.txt")) {
						file_put_contents($this->getDataFolder().$sender->getPlayer()->getName()."_refile.txt", file_get_contents($this->getDataFolder().$sender->getPlayer()->getName()."_refile.txt")-$args[2]);
						$sender->getInventory()->addItem(Item::get(322, 0, $args[2]));
						$sender->sendMessage("§8• §7Pomyslnie wyplaciles §a$args[2] §7refow ze schowka! §8•");
					} else {
						$sender->sendMessage("§8• §7Nie posiadasz takiej ilosci refow aby wyplacic! §8•");
						return false;
					}
				} else {
					$sender->sendMessage("§8•§7 poprawne uzycie: /schowek wyplac refy <ilosc> §8•");
				}
			}
			if($args[0] == "wyplac" && $args[1] == "perly") {
				if(is_numeric($args[2])) {
					if($args[2] <= file_get_contents($this->getDataFolder().$sender->getPlayer()->getName()."_perly.txt")) {
						file_put_contents($this->getDataFolder().$sender->getPlayer()->getName()."_perly.txt", file_get_contents($this->getDataFolder().$sender->getPlayer()->getName()."_perly.txt")-$args[2]);
						$sender->getInventory()->addItem(Item::get(332, 0, $args[2]));
						$sender->sendMessage("§8• §7Pomyslnie wyplaciles §a$args[2] §7perel ze schowka! §8•");
					} else {
						$sender->sendMessage("§8• §7Nie posiadasz takiej ilosci perel aby wyplacic! §8•");
						return false;
					}
				} else {
					$sender->sendMessage("§8•§7 poprawne uzycie: /schowek wyplac perly <ilosc> §8•");
				}
			}
			if($args[0] == "stan") {
				$sender->sendMessage("§8• §7Twoj stan schowka: §8•");
				$sender->sendMessage("§8• §7[§a".file_get_contents($this->getDataFolder().$sender->getPlayer()->getName()."_koxy.txt")."§7] koxow §8•");
				$sender->sendMessage("§8• §7[§a".file_get_contents($this->getDataFolder().$sender->getPlayer()->getName()."_refile.txt")."§7] refilow §8•");
				$sender->sendMessage("§8• §7[§a".file_get_contents($this->getDataFolder().$sender->getPlayer()->getName()."_perly.txt")."§7] perel §8•");
				return true;
			}
			if($args[0] == "wplac" && $args[1] == "koxy") {
				if(is_numeric($args[2])) {
					if($sender->getInventory()->contains(Item::get(466, 1, $args[2]))) {
						file_put_contents($this->getDataFolder().$sender->getPlayer()->getName()."_koxy.txt", file_get_contents($this->getDataFolder().$sender->getPlayer()->getName()."_koxy.txt")+$args[2]);
						$sender->getInventory()->removeItem(Item::get(466, 1, $args[2]));
						$sender->sendMessage("§8• §7Pomyslnie wplaciles §a$args[2] §7koxow do schowka! §8•");
						return true;
					} else {
						$sender->sendMessage("§8• §7Nie posiadasz takiej ilosci koxow w ekwipunku aby wplacic! §8•");
						return false;
					}
				} else {
					$sender->sendMessage("§8• §7Poprawne uzycie: /schowek wplac koxy <ilosc> §8•");
				}
			}
			if($args[0] == "wplac" && $args[1] == "refy") {
				if(is_numeric($args[2])) {
					if($sender->getInventory()->contains(Item::get(322, 0, $args[2]))) {
						file_put_contents($this->getDataFolder().$sender->getPlayer()->getName()."_refile.txt", file_get_contents($this->getDataFolder().$sender->getPlayer()->getName()."_refile.txt")+$args[2]);
						$sender->getInventory()->removeItem(Item::get(322, 0, $args[2]));
						$sender->sendMessage("§8• §7Pomyslnie wplaciles §a$args[2] §7refow do schowka! §8•");
						return true;
					} else {
						$sender->sendMessage("§8• §7Nie posiadasz takiej ilosci refow w ekwipunku aby je wylacic! §8•");
						return false;
					}
				} else {
					$sender->sendMessage("§8• §7Poprawne uzycie: /schowek wplac refil <ilosc> §8•");
				}
			}
			if($args[0] == "wplac" && $args[1] == "perly") {
				if(is_numeric($args[2])) {
					if($sender->getInventory()->contains(Item::get(332, 0, $args[2]))) {
						file_put_contents($this->getDataFolder().$sender->getPlayer()->getName()."_perly.txt", file_get_contents($this->getDataFolder().$sender->getPlayer()->getName()."_perly.txt")+$args[2]);
						$sender->getInventory()->removeItem(Item::get(332, 0, $args[2]));
						$sender->sendMessage("§8• §7Pomyslnie wplaciles §a$args[2] §7perel do schowka! §8•");
						return true;
					} else {
						$sender->sendMessage("§8• §7Nie posiadasz takiej ilosci perel w ekwipunku aby je wplacic! §8•");
						return false;
					}
				} else {
					$sender->sendMessage("§8• §7Poprawne uzycie: /wplac refil <ilosc> §8•");
				}
			}
		}
	}
}