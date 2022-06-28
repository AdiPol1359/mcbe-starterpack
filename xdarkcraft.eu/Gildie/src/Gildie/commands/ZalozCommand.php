<?php

namespace Gildie\commands;

use pocketmine\command\{
    Command, CommandSender
};
use Gildie\utils\ShapesUtils;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\Player;
use Gildie\Main;
use Core\Main as CoreMain;
use Gildie\guild\GuildManager;

class ZalozCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("zaloz", "Komenda zaloz");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if(!$this->canUse($sender))
            return;

    	$nick = $sender->getName();

        if(!$sender instanceof Player) {
            $sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
            return;
        }

        $guildManager = Main::getInstance()->getGuildManager();

        if(!isset($args[1])) {
            $sender->sendMessage(Main::format("Poprawne uzycie: /zaloz (tag) (nazwa)"));
            return;
        }

        if($guildManager->isInGuild($sender->getName())) {
            $sender->sendMessage(Main::format("Jestes juz w gildii!"));
            return;
        }

        $border = floor(CoreMain::BORDER / 2);

        $x = $sender->getFloorX();
        $z = $sender->getFloorZ();
        if($x >= $border || $x <= -$border || $z >= $border || $z <= -$border) {
            $sender->sendMessage(Main::format("Nie mozesz zalozyc gildii za borderem mapy!"));
            return;
        }

        $lvl = 0;

        if(!($sender->isOp() || $sender->hasPermission("nicecraft.gildie.op"))){
         foreach(GuildManager::getItems($sender) as $item){
          if($item->getId() == 466) {
		        	$db = $sender->getServer()->getPluginManager()->getPlugin("Core")->getDb();
		        	$array = $db->query("SELECT * FROM depozyt WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);
		           	
		        	$invCount = 0;
		           	
		        	foreach($sender->getInventory()->getContents() as $invItem)
		        	 if($invItem->getId() == Item::ENCHANTED_GOLDEN_APPLE)
		           $invCount += $invItem->getCount();
		         	$depozytCount = $array["koxy"];
		        	if($invCount < $item->getCount() && ($invCount + $depozytCount) < $item->getCount()) {
		 	           $sender->sendMessage(Main::format("Nie posiadasz wszystkich itemow na gildie!"));
               return;
         }
		 } elseif($item->getId() == Item::GOLDEN_APPLE) {
		        	$db = $sender->getServer()->getPluginManager()->getPlugin("Core")->getDb();
		        	$array = $db->query("SELECT * FROM depozyt WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);
		           	
		        	$invCount = 0;
		           	
		        	foreach($sender->getInventory()->getContents() as $invItem)
		        	 if($invItem->getId() == Item::GOLDEN_APPLE)
		           $invCount += $invItem->getCount();
		         	$depozytCount = $array["refy"];
		        	if($invCount < $item->getCount() && ($invCount + $depozytCount) < $item->getCount()) {
		 	           $sender->sendMessage(Main::format("Nie posiadasz wszystkich itemow na gildie!"));
               return;
         }
		 } else {
		 
                if(!$sender->getInventory()->contains($item)){
                    $sender->sendMessage(Main::format("Nie posiadasz wszystkich itemow na gildie!"));
                    return;
                }
            }
}

            $lvl = 100;

          		if($sender->hasPermission("nicecraft.gildie.vip"))
	          	 $lvl = 80;
		 
	          	if($sender->hasPermission("nicecraft.gildie.svip"))
		           $lvl = 60;
		
		          if($sender->hasPermission("nicecraft.gildie.sponsor"))
		           $lvl = 50;

            if($sender->getXpLevel() < $lvl) {
                $sender->sendMessage(Main::format("Nie posiadasz wymaganago poziomu doswiadczenia!"));
                return;
            }
        }

        if(strlen($args[0]) > 4) {
            $sender->sendMessage("§8§l>§r §7Tag gildii jest za dlugi! Moze wynosic max §44 §7znaki");
            return;
        }

        if(!ctype_alnum($args[0])) {
            $sender->sendMessage("§8§l>§r §7Tag gildii moze zawierac tylko litery i cyfry");
            return;
        }

        if(strlen($args[1]) > 30) {
            $sender->sendMessage("§8§l>§r §7Tag gildii jest za dlugi! moze wynosic max §430 §7znakow");
            return;
        }

        if($guildManager->isGuildExists($args[0])) {
            $sender->sendMessage("§8§l>§r §7Ta gildia juz istnieje!");
            return;
        }

        if($sender->getPosition()->distance($sender->getLevel()->getSafeSpawn()) < 300) {
            $sender->sendMessage("§8§l>§r §7Gildie mozna zakladac §4300 §7kratek od spawnu");
            return;
        }

        $maxSize = 79;

        $x = $sender->getFloorX();
        $z = $sender->getFloorZ();

        $arm = floor($maxSize / 2);

        $max_x1 = $x + $arm;
        $max_z1 = $z + $arm;
        $max_x2 = $x - $arm;
        $max_z2 = $z - $arm;

        if($guildManager->isMaxPlot($max_x1, $max_z1) || $guildManager->isMaxPlot($max_x2, $max_z2) || $guildManager->isMaxPlot($max_x2, $max_z1) || $guildManager->isMaxPlot($max_x1, $max_z2)) {
            $sender->sendMessage("§8§l>§r §7Jestes zbyt blisko innej gildii!");
            return;
        }


        $sender->getServer()->broadcastMessage(Main::format("Gilia §8[§4$args[0]§8] - §4$args[1] §7zostala zalozona przez gracza §4{$sender->getName()}"));

        Main::getInstance()->getDb()->query("INSERT INTO players (player, guild, rank) VALUES ('{$sender->getName()}', '$args[0]', 'Leader')");

        $defaultSize = 35;

        $arm = floor($defaultSize / 2);

        $x1 = $x + $arm;
        $z1 = $z + $arm;
        $x2 = $x - $arm;
        $z2 = $z - $arm;

        Main::getInstance()->getDb()->query("INSERT INTO plots (guild, size, x1, z1, x2, z2, max_x1, max_z1, max_x2, max_z2) VALUES ('$args[0]', '$defaultSize', '$x1', '$z1', '$x2', '$z2', '$max_x1', '$max_z1', '$max_x2', '$max_z2')");

        $date = date_create(date("G:i:s"));
        date_add($date,date_interval_create_from_date_string("3 days"));
        $date =  date_format($date,"d.m.Y H:i:s");

        Main::getInstance()->getDb()->query("INSERT INTO guilds (guild, name, lifes, base_x, base_y, base_z, heart_x, heart_y, heart_z, conquer_date, expiry_date, pvp_guild, pvp_alliances) VALUES ('$args[0]', '$args[1]', '3', '{$sender->getX()}', '32', '{$sender->getZ()}', '{$sender->getFloorX()}', '31', '{$sender->getFloorZ()}', '$date', '$date', 'off', 'off')");

        $heartPosition = new Position($sender->getFloorX(), 31, $sender->getFloorZ(), $sender->getLevel());

        $sender->teleport($heartPosition->add(0, 1));

        ShapesUtils::createGuildShape($heartPosition);

        $sender->getLevel()->setBlock($heartPosition, Block::get(Block::END_PORTAL_FRAME));

        $guildManager->setAllPermissions($nick);

        $guildManager->updateNameTags();

        Main::getInstance()->getSkarbiecConfig()->set($args[0], []);
        Main::getInstance()->getSkarbiecConfig()->save();

        if(!($sender->isOp() or $sender->hasPermission("nicepe.gildie.op"))){
            foreach(GuildManager::getItems($sender) as $item) {
            	if($item->getId() == Item::ENCHANTED_GOLDEN_APPLE) {
		        	$db = $sender->getServer()->getPluginManager()->getPlugin("Core")->getDb();
		        	$array = $db->query("SELECT * FROM depozyt WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);
		           	
		        	$invCount = 0;
		           	
		        	foreach($sender->getInventory()->getContents() as $invItem)
		        	 if($invItem->getId() == Item::ENCHANTED_GOLDEN_APPLE)
		           $invCount += $invItem->getCount();
		         	$depozytCount = $array["koxy"];
		         	
		        	if($invCount >= $item->getCount()) {
		        	 $sender->getInventory()->removeItem($item);
		        	} else {
		        		$sender->getInventory()->removeItem(Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, $invCount));
		        		$toRemove = $item->getCount() - $invCount;
		        		$db->query("UPDATE depozyt SET koxy = koxy - '$toRemove' WHERE nick = '$nick'");
		        	}
		 } elseif($item->getId() == Item::GOLDEN_APPLE) {
		        	$db = $sender->getServer()->getPluginManager()->getPlugin("Core")->getDb();
		        	$array = $db->query("SELECT * FROM depozyt WHERE nick = '$nick'")->fetchArray(SQLITE3_ASSOC);
		           	
		        	$invCount = 0;
		           	
		        	foreach($sender->getInventory()->getContents() as $invItem)
		        	 if($invItem->getId() == Item::GOLDEN_APPLE)
		           $invCount += $invItem->getCount();
		         	$depozytCount = $array["refy"];
		         	
		        	if($invCount >= $item->getCount()) {
		        	 $sender->getInventory()->removeItem($item);
		        	} else {
		        		$sender->getInventory()->removeItem(Item::get(Item::GOLDEN_APPLE, 0, $invCount));
		        		$toRemove = $item->getCount() - $invCount;
		        		$db->query("UPDATE depozyt SET refy = refy - '$toRemove' WHERE nick = '$nick'");
		        	}
		 } else 
		 $sender->getInventory()->removeItem($item);
         }
            $sender->setXpLevel($sender->getXpLevel() - $lvl);
        }
    }
}