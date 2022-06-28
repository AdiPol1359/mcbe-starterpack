<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};

use pocketmine\entity\Entity;

use pocketmine\nbt\tag\{CompoundTag, ListTag};

use Core\Main;

class VshopCommand extends CoreCommand {
	
	public function __construct() {
		parent::__construct("vshop", "Komenda vshop", true);
	}
	
	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
		    return;
		
		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}
		
		if(empty($args)){
			$sender->sendMessage(Main::formatLines(["§4/vshop create §8(§4nazwa§8) - §7Tworzy villagera", "§4/vshop remove §8- §7Usuwa villagera", "§4/vshop changename §8(§4nazwa§8) - §7Zmienia nazwe villagera", "§4/vshop copy §8 - §7Kopiuje villagera", "§4/vshop addrecipe §8- §7Dodaje item do villagera", "§4/vshop removerecipe §8(§4nazwa§8) - §7Usuwa item z villagera", "§4/vshop tp §8- §7Teleportuje villagera w wybrane miejsce"]));
			return;
		}
		
		switch($args[0]){
			case "create":
			$nbt = Entity::createBaseNBT($sender->asVector3(), null, $sender->getYaw());

			if(isset($args[1])){
				array_shift($args);
				$name = implode(" ", $args);
				
				$nbt->setString("CustomName", $name);
			}
			
			$villager = Entity::createEntity("Villager", $sender->getLevel(), $nbt);
			$villager->spawnToAll();
			
			$sender->sendMessage(Main::format("Villager o nazwie §4{$villager->getCustomName()} §r§7zostal utworzony!"));
			break;
			
			case "remove":
			Main::$removeVillager[$sender->getName()] = true;
			
			$sender->sendMessage("§8§l>§r §7Kliknij w villagera ktorego chcesz usunac!");
			break;
			
			case "changename":
			if(!(isset($args[1]))){
				$sender->sendMessage(Main::format("Poprawne uzycie: /vshop changename §8(§4nazwa§8)"));
				return;
			}
			
			array_shift($args);
			$name = implode(" ", $args);
			
			Main::$villagerName[$sender->getName()] = $name;
			
			$sender->sendMessage("§8§l>§r §7Kliknij w villagera ktoremu chcesz zmienic nazwe");
			break;
			
			case "addrecipe":
			if(isset(Main::$addVillagerRecipe[$sender->getName()])) {
				$sender->sendMessage("§8§l>§r §7Dodawanie receptury zostalo przerwane!");
				
				unset(Main::$addVillagerRecipe[$sender->getName()]);
				return;
			}
			
			Main::$addVillagerRecipe[$sender->getName()] = [];
			
			$sender->sendMessage("§8§l>§r §7Kliknij na villagera ktoremu chcesz dodac recepture!");
			break;
			
			case "removerecipe":
			if(!(isset($args[1]))){
				$sender->sendMessage(Main::format("Poprawne uzycie: /vshop removerecipe §8(§4strona§8)"));
				return;
			}
			
			if(!(is_numeric($args[1]))){
				$sender->sendMessage(Main::format("Argument §41 §7musi byc numeryczny!"));
				return;
			}
			
			Main::$removeVillagerRecipe[$sender->getName()] = $args[1];
			
			$sender->sendMessage("§8§l>§r §7Kliknij na villagera ktoremu chcesz usunac recepture");
			break;
			
			case "tp":
			
			Main::$tpVillager[$sender->getName()] = true;
			
			$sender->sendMessage("§8§l>§r §7Kliknij na villagera ktorego chcesz przeteleportowac");
			break;
			
			case "copy":
			
			Main::$copyVillager[$sender->getName()] = true;
			
			$sender->sendMessage("§8§l>§r §7Kliknij na villagera ktorego chcesz skopiowac");
			
			break;
		}
	}
}