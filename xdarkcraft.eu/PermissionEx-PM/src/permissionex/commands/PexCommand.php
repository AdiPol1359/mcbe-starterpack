<?php

declare(strict_types=1);

namespace permissionex\commands;

use pocketmine\{
	Server, Player
};
use pocketmine\command\{
	Command, CommandSender
};
use permissionex\Main;
use permissionex\group\GroupManager;
use permissionex\managers\ChatManager;

class PexCommand extends Command {

	public function __construct() {
		parent::__construct("pex", "Pex Command");
		$this->setPermission("xdarkcraft.command.pex");
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		$groupManager = Main::getInstance()->getGroupManager();

		if(empty($args) || isset($args[0]) && $args[0] == "help") {
			if(!$sender->hasPermission("pex.command.general")) {
				$sender->sendMessage(Main::getPermissionMessage());
				return;
			}
			
		 $sender->sendMessage("§7PermissionEx help:");
		 $sender->sendMessage("§7() §8- §7required");
		 $sender->sendMessage("§7{} §8- §7optional");
		 $sender->sendMessage(" ");
		 $sender->sendMessage("§8".str_repeat('-',38)."(§2GENERAL§8)".str_repeat('-',39));
		 $sender->sendMessage("§7/pex info §8- §7shows plugin informations");
		 $sender->sendMessage("§7/pex help §8- §7shows a list of commands");
		 $sender->sendMessage("§7/pex reload §8- §7reloads plugin");
		 $sender->sendMessage("§7/pex set default group (group) §8- §7sets default group");
		 $sender->sendMessage("§7/pex set chat pw (true/false) §8- §7sets chat per world status");
		 $sender->sendMessage("§8".str_repeat('-',41)."(§2USER§8)".str_repeat('-',41));
		 $sender->sendMessage("§7/pex user §8- §7shows a list of registered users");
		 $sender->sendMessage("§7/pex user (nick) §8- §7shows a list of player groups");
		 $sender->sendMessage("§7/pex user (nick) list §8- §7shows a list of player permissions");
		 $sender->sendMessage("§7/pex user (nick) list §8- §7Delete user data");
		 $sender->sendMessage("§7/pex user (nick) add (permission) {time[s/m/h/d]} §8- §7gives player permission");
		 $sender->sendMessage("§7/pex user (nick) remove (permission) §8- §7remove player permission");
		 $sender->sendMessage("§7/pex user (nick) group set (group) §8- §7sets the player’s group");
		 $sender->sendMessage("§7/pex user (nick) group add (group) {time[s/m/h/d]} §8- §7adds the player’s group");
		 $sender->sendMessage("§7/pex user (nick) group remove (group) §8- §7removes the player’s group");
		 $sender->sendMessage("§7/pex user (nick) world (worldName) group set (group) §8- §7sets the player’s group on specify world");
		 $sender->sendMessage("§7/pex user (nick) world (worldName) group add (group) {time[s/m/h/d]} §8- §7adds the player’s group on specify world");
		 $sender->sendMessage("§7/pex user (nick) world (worldName) group remove (group) §8- §7removes the player’s group on specify world");
		 $sender->sendMessage("§8".str_repeat('-',40)."(§2GROUP§8)".str_repeat('-',40));
		 $sender->sendMessage("§7/pex group §8- §7shows a list of registered groups");
		 $sender->sendMessage("§7/pex group (group) list §8- §7shows a list of group’s permissions");
		 $sender->sendMessage("§7/pex group (group) players §8- §7shows a list of group’s players");
		 $sender->sendMessage("§7/pex group (group) format set (format) §8- §7sets group chat format");
		 $sender->sendMessage("§7/pex group (group) format remove §8- §7removes group chat format");
		 $sender->sendMessage("§7/pex group (group) rank (rank) §8- §7sets group rank in hierarchy");
		 $sender->sendMessage("§7/pex group (group) displayname §8- §7group displayname settings");
		 $sender->sendMessage("§7/pex group (group) nametag §8- §7group nametag settings");
		 $sender->sendMessage("§7/pex group (group) create {parents} §8- §7creates a group");
		 $sender->sendMessage("§7/pex group (group) delete §8- §7deletes a group");
		 $sender->sendMessage("§7/pex group add (permission) §8- §7adds permission to the group");
		 $sender->sendMessage("§7/pex group (group) remove (permission) §8- §7removes permission from group");
		 $sender->sendMessage("§7/pex group (group) parents set (parent) §8- §7sets the parent of the group");
		 $sender->sendMessage("§7/pex group (group) parents add (parent) §8- §7adds the parent to the group");
		 $sender->sendMessage("§7/pex group (group) parents remove (parent) §8- §7removes the parent from the group");
		 return;
		}

		switch($args[0]) {
			case "info":
			 if(!$sender->hasPermission("pex.command.general")) {
			  $sender->sendMessage(Main::getPermissionMessage());
			 	return;
		 	}
		 	
			 $sender->sendMessage("§7Plugin name: §2PermissionEx");
			 $sender->sendMessage("§7Version: §2".Main::VERSION);
			 $sender->sendMessage("§7Author: §2xStrixU");
			 $sender->sendMessage(" ");
			 $sender->sendMessage("§7Contact me:");
			 $sender->sendMessage("§7YouTube: §2xStrixU");
			 $sender->sendMessage("§7Discord: §2xStrixU#4844");
			break;
			
			case "reload":
			 if(!$sender->hasPermission("pex.command.reload")) {
			  $sender->sendMessage(Main::getPermissionMessage());
			 	return;
		 	}
		 	Main::getInstance()->reload();
		 	$sender->sendMessage(Main::format("Plugin has been reloaded"));
			break;
			
			case "set":
		 	if(!$sender->hasPermission("pex.command.set")) {
		 		$sender->sendMessage(Main::getPermissionMessage());
	 			return;
		 	}
		 	
		 	if(!isset($args[1])) {
		 		$sender->sendMessage(Main::getErrorMessage());
		 		return;
		 	}
			 switch($args[1]) {
			 	case "chat":
			 	 if(!isset($args[2])) {
			 	 	$sender->sendMessage(Main::getErrorMessage());
			 	 	return;
			 	 }
			 	 
			 	 switch($args[2]) {
			 	 	case "pw":
			 	 	 if(!isset($args[3]) || (isset($args[3]) && !in_array($args[3], ["true", "false"]))) {
			 	 	 	$sender->sendMessage(Main::format("Usage: /pex set chat pw (true/false)"));
			 	 	 	return;
			 	 	 }
			 	 	 
			 	 	 if($args[3] == "false") {
			 	 	 	ChatManager::setChatPerWorld(false);
			 	 	 	$sender->sendMessage(Main::format("Chat per world has been unsetted"));
			 	 	 } elseif($args[3] == "true") {
			 	 	 	ChatManager::setChatPerWorld();
			 	 	 	$sender->sendMessage(Main::format("Chat per world has been setted"));
			 	 	 }
			 	 	break;
			 	 	default:
			 	 	 $sender->sendMessage(Main::getErrorMessage());
			 	 }
			 	break;
			 	case "default":
			 	 switch($args[2]) {
			 	 	case "group":
			 	 	 if(!isset($args[3])) {
			 	 	 	$sender->sendMessage("Usage: /pex set default group (group)");
			 	 	 	return;
			 	 	 }
			 	 	 
			 	 	 if(!$groupManager->isGroupExists($args[3])) {
			 	 	 	$sender->sendMessage(Main::format("Group not found!"));
			 	 	 	return;
			 	 	 }
			 	 	 
			 	 	 $groupManager->setDefaultGroup($groupManager->getGroup($args[3]));
			 	 	 $sender->sendMessage(Main::format("Setted default group to ”§2{$args[3]}§7”"));
			 	 	break;
			 	 	default:
			 	   $sender->sendMessage(Main::getErrorMessage());
			 	 }
			 	break;
			 	default:
			 	 $sender->sendMessage(Main::getErrorMessage());
			 }
			break;
			
			
			case "group":
	 		 if(!$sender->hasPermission("pex.command.groups")) {
		 		$sender->sendMessage(Main::getPermissionMessage());
			 	return;
		 	}
		 	
			 if(!isset($args[1])) {
			  $sender->sendMessage("§7Registered groups: ");
			  foreach($groupManager->getAllGroups() as $group) {
			 	
			 	 $parentsFormat = function($group) : string {
			 		 $format = "";
			 		
			 	 	foreach($group->getParents() as $g)
			 		  $format .= $g->getName().", ";
			 		 
			 		 if($format != "")
			 	  	$format = substr($format, 0, strlen($format) - 2);
 
			  	 return $format;
			  	};
			  	
			 	 $sender->sendMessage(" §7{$group->getName()} #{$group->getRank()} §2[{$parentsFormat($group)}]");
			 	}
			 	return;
			 }
			 
			 if(!isset($args[2])) {
			 	$sender->sendMessage(Main::getErrorMessage());
			 	return;
			 }
			 
			 switch($args[2]) {
			 	case "nametag":
			 	 if(!$groupManager->isGroupExists($args[1])) {
		  	 	$sender->sendMessage(Main::format("This group does not exists!"));
			   	return;
		  	 }
		  	 
		  	 $group = $groupManager->getGroup($args[1]);
		  	 
		  	 if(!isset($args[3])) {
		  	 	$nametag = $group->getNametag();
		  	 	
		  	 	$sender->sendMessage(Main::format($nametag == null ? "This group has no nametag!" : "Group §2{$args[1]} §7nametag: §2{$nametag}"));
		  	 	return;
		  	 }
		  	 
		  	 switch($args[3]) {
		  	 	case "set":
		  	 	 if(!isset($args[4])) {
		  	 	 	$sender->sendMessage(Main::format("Usage: /pex group $args[1] nametag set (nametag format)"));
		  	 	 	return;
		  	 	 }
		  	 	 
		  	 	 $group->setNametag($args[4]);	 
		  	   $sender->sendMessage(Main::format("Group §2{$args[1]} §7nametag has been set to §2{$args[4]}"));
		  	 	break;
		  	 	
		  	 	case "remove":
		  	 	 $group->setNametag();
		  	   $sender->sendMessage(Main::format("Group §2{$args[1]} §7nametag has been removed"));
		  	 	break;
		  	 	
		  	 	default:
		  	 	 $sender->sendMessage(Main::getErrorMessage());
		  	 }
			 	break;
			 	
			 	case "displayname":
			 	 if(!$groupManager->isGroupExists($args[1])) {
		  	 	$sender->sendMessage(Main::format("This group does not exists!"));
			   	return;
		  	 }
		  	 
		  	 $group = $groupManager->getGroup($args[1]);
		  	 
		  	 if(!isset($args[3])) {
		  	 	$displayName = $group->getDisplayName();
		  	 	
		  	 	$sender->sendMessage(Main::format($displayName == null ? "This group has no displayname!" : "Group §2{$args[1]} §7displayname: §2{$displayName}"));
		  	 	return;
		  	 }
		  	 
		  	 switch($args[3]) {
		  	 	case "set":
		  	 	 if(!isset($args[4])) {
		  	 	 	$sender->sendMessage(Main::format("Usage: /pex group $args[1] displayname set (displayname)"));
		  	 	 	return;
		  	 	 }
		  	 	 
		  	 	 $group->setDisplayName($args[4]);	 
		  	   $sender->sendMessage(Main::format("Group §2{$args[1]} §7displayname has been set to §2{$args[4]}"));
		  	 	break;
		  	 	
		  	 	case "remove":
		  	 	 $group->setDisplayName();
		  	   $sender->sendMessage(Main::format("Group §2{$args[1]} §7displayname has been removed"));
		  	 	break;
		  	 	
		  	 	default:
		  	 	 $sender->sendMessage(Main::getErrorMessage());
		  	 }
			 	break;
			 	case "list":
			 	 if(!$groupManager->isGroupExists($args[1])) {
		  	 	$sender->sendMessage(Main::format("This group does not exists!"));
			   	return;
		  	 }
		  	 
		  	 $group = $groupManager->getGroup($args[1]);
		  	 
			 	 $sender->sendMessage("§7Group ”§2{$args[1]}§7” permissions:");
			 	 

    	 foreach($group->getPermissions() as $permission) {
    	 	foreach($group->getParents() as $parentGroup) {
    	 	 if($parentGroup->hasPermission($permission)) {
    	 	 	$sender->sendMessage(" §7{$permission} ({$parentGroup->getName()})");
    	 	 	continue;
    	 	 }
                $sender->sendMessage(" §7{$permission} (own)");
    	 	}
    	 }
			 	break;
			 	
			 	case "players":
			 	 if(!$groupManager->isGroupExists($args[1])) {
			   	$sender->sendMessage(Main::format("This group does not exists!"));
			 	  return;
			   }
			   
			 	 $sender->sendMessage("§7Group ”§2{$args[1]}§7” players:");
    	 foreach($groupManager->getGroup($args[1])->getPlayers() as $nick)
    	  $sender->sendMessage(" §7{$nick}");
			 	break;
			 	
			 	case "format":
			 	 if(!$groupManager->isGroupExists($args[1])) {
			   	$sender->sendMessage(Main::format("This group does not exists!"));
			 	  return;
			   }
			   
			   if(!isset($args[3])) {
			   	$sender->sendMessage(Main::getErrorMessage());
			   	return;
			   }
			   
			 	 switch($args[3]) {
			 	 	case "set":
			 	 	 if(!isset($args[4])) {
			 	 	 	$sender->sendMessage(Main::format("Usage: /pex group $args[1] format set (format)"));
			 	 	 	return;
			 	 	 }
			 	 	 
			 	 	 $groupManager->getGroup($args[1])->setFormat(str_replace('&', '§', $args[4]));
			 	 	 
			 	 	 $sender->sendMessage(Main::format("Group format updated!"));
			 	 	break;
			 	 	
			 	 	case "remove":
			 	 	 $groupManager->getGroup($args[1])->removeFormat();
			 	 	 
			 	 	 $sender->sendMessage(Main::format("Group format removed!"));
			 	 	break;
			 	 	
			 	 	default:
			 	 	 $sender->sendMessage(Main::getErrorMessage());
			 	 }
			 	break;
			 	
			 	case "rank":
			 	 if(!$groupManager->isGroupExists($args[1])) {
			   	$sender->sendMessage(Main::format("This group does not exists!"));
			 	  return;
			   }
			   
      if(!isset($args[3])) {
      	$sender->sendMessage(Main::format("Usage: /pex group $args[1] rank (rank)"));
      	return;
      }
      
      if(!is_numeric($args[3])) {
      	$sender->sendMessage(Main::format("Rank must be numeric!"));
      	return;
      }
      
      $groupManager->getGroup($args[1])->setRank((int) $args[3]);
      
      $sender->sendMessage(Main::format("{$args[1]}’s rank set to #{$args[3]}"));
     break;
			 	
			 	case "create":
			 	 $parents = [];
			 	 
			 	 if($groupManager->isGroupExists($args[1])) {
			 	 	$sender->sendMessage(Main::format("This group is already exists!"));
			 	 	return;
			 	 }
			 	 
			 	 if(isset($args[3])) {
			 	 	$arg = str_replace(' ', '', strtolower($args[3]));
			 	 	$parents = explode(',', $arg);
			 	 	
			 	 	foreach($parents as $parentGroup)
			 	 	 if(!$groupManager->isGroupExists($parentGroup))
			 	 	  unset($parents[array_search($parentGroup, $parents)]);
			 	 }
			 	 
			 	 $groupManager->createGroup($args[1], $parents);
			 	 $sender->sendMessage(Main::format("Group ”§2{$args[1]}§7” created!"));
			 	break;
			 	
			 	case "delete":
			 	 if(!$groupManager->isGroupExists($args[1])) {
			   	$sender->sendMessage(Main::format("This group does not exists!"));
			 	  return;
			   }
			   
			 	 $groupManager->getGroup($args[1])->delete();
			 	 $sender->sendMessage(Main::format("Group ”§2{$args[1]}§7” deleted!"));
			 	break;
			 	
			 	case "add":
			 	 if(!$groupManager->isGroupExists($args[1])) {
			   	$sender->sendMessage(Main::format("This group does not exists!"));
			 	  return;
			   }
			   
			 	 if(!isset($args[3])) {
			 	 	$sender->sendMessage(Main::format("Usage: /pex group $args[1] add (permission)"));
			 	 	return;
			 	 }
			 	 $perm = strtolower($args[3]);
			 	 
			 	 $groupManager->getGroup($args[1])->addPermission($perm);
			 	 $sender->sendMessage(Main::format("Permission ”§2{$perm}§7” added to ”§2{$args[1]}§7”!"));
			 	break;
			 	
			 	case "remove":
			 	 if(!$groupManager->isGroupExists($args[1])) {
			   	$sender->sendMessage(Main::format("This group does not exists!"));
			 	  return;
			   }
			   
			 	 if(!isset($args[3])) {
			 	 	$sender->sendMessage(Main::format("Usage: /pex group $args[1] remove (permission)"));
			 	 	return;
			 	 }
			 	 $perm = strtolower($args[3]);
			 	 
			 	 $groupManager->getGroup($args[1])->removePermission($perm);
			 	 $sender->sendMessage(Main::format("Permission ”§2{$perm}§7” removed from ”§2{$args[1]}§7”!"));
			 	break;
			 	
			 	case "parents":
			 	 if(!$groupManager->isGroupExists($args[1])) {
			   	$sender->sendMessage(Main::format("This group does not exists!"));
			 	  return;
			   }
			 	 if(!isset($args[3])) {
			 	 	$sender->sendMessage(Main::getErrorMessage());
			 	 	return;
			 	 }
			 	 
			 	 switch($args[3]) {
			 	 	case "set":
			 	 	 if(!isset($args[4])) {
			 	 	 	$sender->sendMessage(Main::format("Usage: /pex group $args[1] parents set (parents)"));
			 	 	 	return;
			 	 	 }
			 	 	 
			 	 	 if(!$groupManager->isGroupExists($args[4])) {
			     	$sender->sendMessage(Main::format("This parent group does not exists!"));
			     	return;
			     }
			     
			     if($groupManager->getGroup($args[4])->hasParent($groupManager->getGroup($args[1]))) {
			     	$sender->sendMessage(Main::format("This group can’t be {$args[1]}’s parent!"));
			     	return;
			     }
			 	 	 
			 	 	 $groupManager->getGroup($args[1])->removeParents();
			 	 	 $groupManager->getGroup($args[1])->addParent($groupManager->getGroup($args[4]));
			 	 	 
			 	 	 $sender->sendMessage(Main::format("Parent ”§2{$args[4]}§7” setted to ”§2{$args[1]}§7”!"));
			 	 	break;
			 	 	
			 	 	case "add":
			 	 	 if(!isset($args[4])) {
			 	 	 	$sender->sendMessage(Main::format("Usage: /pex group $args[1] parents add (parents)"));
			 	 	 	return;
			 	 	 }
			 	 	 
			 	 	 if(!$groupManager->isGroupExists($args[4])) {
			     	$sender->sendMessage(Main::format("This parent group does not exists!"));
			     	return;
			     }
			     
			     if($groupManager->getGroup($args[4])->hasParent($groupManager->getGroup($args[1]))) {
			     	$sender->sendMessage(Main::format("Group ”§2{$args[4]}§7” can’t be §2{$args[1]}§7’s parent"));
			     	return;
			     }

			 	 	 $groupManager->getGroup($args[1])->addParent($groupManager->getGroup($args[4]));
			 	 	 
			 	 	 $sender->sendMessage(Main::format("Parent ”§2{$args[4]}§7” added to ”§2{$args[1]}§7”!"));
			 	 	break;
			 	 	
			 	 	case "remove":
			 	 	 if(!isset($args[4])) {
			 	 	 	$sender->sendMessage(Main::format("Usage: /pex group $args[1] parents remove (parents)"));
			 	 	 	return;
			 	 	 }
			 	 	 
			 	 	 if(!$groupManager->isGroupExists($args[4])) {
			     	$sender->sendMessage(Main::format("This parent group does not exists!"));
			     	return;
			     } 
			     
			 	 	 $groupManager->getGroup($args[1])->removeParent($groupManager->getGroup($args[4]));
			 	 	 
			 	 	 $sender->sendMessage(Main::format("Parent ”§2{$args[4]}§7” removed from ”§2{$args[1]}§7”!"));
			 	 	break;
			 	 	
			 	 	default:
			 	 	 $sender->sendMessage(Main::getErrorMessage());
			 	 }
			 	break;
			 	default:
			 	 $sender->sendMessage(Main::getErrorMessage());
			 }
			break;
			
   case "user":
   if(!$sender->hasPermission("pex.command.users")) {
   	$sender->sendMessage(Main::getPermissionMessage());
		 	return;
		 }
		 
   if(!isset($args[1])) {
   	$sender->sendMessage("§7Registered users:");
   	foreach(Main::getInstance()->getProvider()->getAllUsers() as $nick)
   	 $sender->sendMessage(" §7{$nick}");
   	
   	return;
   }
   
   if(!$groupManager->userExists($args[1])) {
   	$sender->sendMessage(Main::format("User not found!"));
   	return;
   }
   
    if(!isset($args[2])) {
     $sender->sendMessage("§7{$args[1]}’s groups:");
     foreach($groupManager->getAllGroups() as $group) {
     	if(!$groupManager->getPlayer($args[1])->hasGroup($group))
      $sender->sendMessage("§2Group §7{$group->getName()}§2: §7doesn’t have");
      else {
      	$expiryTime = $groupManager->getPlayer($args[1])->getGroupExpiry($group);
      	$expiryFormat = GroupManager::expiryFormat($expiryTime);
      	
       $sender->sendMessage("§2Group §7{$group->getName()}§2: §7".($expiryTime == null ? "forever" : "§2{$expiryFormat['days']}§7d §2{$expiryFormat['hours']}§7h §2{$expiryFormat['minutes']}§7m §2{$expiryFormat['seconds']}§7s"));
      }
     }
     return;
    }
    
    switch($args[2]) {
    	case "world":
    	 if(!isset($args[4])) {
    	 	$sender->sendMessage(Main::getErrorMessage());
    	 	return;
    	 }
    	 
    	 $levelName = $args[3];
    	 
    	 switch($args[4]) {
    	 	case "add":
    	   if(!isset($args[5])) {
    	   	$sender->sendMessage(Main::format("Usage: /pex user $args[1] world $args[5] add (permission) {time[s/m/h/d]}"));
    	   	return;
    	   }
    	   $time = null;
        
        if(isset($args[6])) {
  	      if(strpos($args[6], "d"))
		        $time = intval(explode("d", $args[6])[0]) * 86400;
            
         if(strpos($args[6], "h"))
	 	       $time = intval(explode("h", $args[6])[0]) * 3600;

	   	    if(strpos($args[6], "m"))
		        $time = intval(explode("h", $args[6])[0]) * 60;

	 	      if(strpos($args[6], "s"))
	 	       $time = intval(explode("s", $args[6])[0]);
	 	      $playerManager = $groupManager->getPlayer($args[1])->addPermission($args[5], $time);
        } else
         $playerManager = $groupManager->getPlayer($args[1])->addPermission($args[5]);
    	 
    	   $sender->sendMessage(Main::format("Permission ”§2{$args[5]}§7” added to world §2{$levelName}§7".($time == null ? "" : " for §2{$args[6]}")));
    	  break;
    	
      	case "group":
        if(!isset($args[6])) {
         $sender->sendMessage(Main::getErrorMessage());
         return;
        }
      
        if(!$groupManager->isGroupExists($args[6])) {
         $sender->sendMessage(Main::format("This group does not exists!"));
         return;
        }
      
        switch($args[5]) {
        	case "add":  
          $playerManager = $groupManager->getPlayer($args[1]);
        
          $player = $playerManager->getPlayer();
          $nick = $player instanceof Player ? $player->getName() : $args[1];
        
          $time = null;
        
          if(isset($args[7])) {
  	        if(strpos($args[7], "d"))
		          $time = intval(explode("d", $args[7])[0]) * 86400;
          
          	if(strpos($args[7], "h"))
	 	         $time = intval(explode("h", $args[7])[0]) * 3600;

	         	if(strpos($args[7], "m"))
		          $time = intval(explode("h", $args[7])[0]) * 60;

	 	        if(strpos($args[7], "s"))
	 	         $time = intval(explode("s", $args[7])[0]);
	 	        $playerManager->addGroup($groupManager->getGroup($args[6]), $time, $levelName);
          } else
           $playerManager->addGroup($groupManager->getGroup($args[6]), null, $levelName);
        
          $sender->sendMessage(Main::format("User §2{$nick} §7added to group on world §2{$levelName} §7”§2{$args[6]}§7”".($time == null ? "" : " for §2{$args[7]}")));
         break;
   
         case "set":
          $playerManager = $groupManager->getPlayer($args[1]);
    
          $player = $playerManager->getPlayer();
          $nick = $player instanceof Player ? $player->getName() : $args[1];
    
          $time = null;
        
          if(isset($args[7])) {
  	        if(strpos($args[7], "d"))
		          $time = intval(explode("d", $args[7])[0]) * 86400;
          
           if(strpos($args[7], "h"))
	 	         $time = intval(explode("h", $args[7])[0]) * 3600;

	         	if(strpos($args[7], "m"))
		          $time = intval(explode("h", $args[7])[0]) * 60;

	 	        if(strpos($args[7], "s"))
	 	         $time = intval(explode("s", $args[7])[0]);
	 	        $playerManager->setGroup($groupManager->getGroup($args[6]), $time, $levelName);
          } else
           $playerManager->setGroup($groupManager->getGroup($args[6]), null, $levelName);
        
          $sender->sendMessage(Main::format("{$nick}’s group set to ”§2{$args[4]}§7” on world §2{$levelName}§7".($time == null ? "" : " for §2{$args[5]}")));
           
           break;
           
           case "remove":      
            $playerManager = $groupManager->getPlayer($args[1]);
            
            $player = $playerManager->getPlayer();
            $nick = $player instanceof Player ? $player->getName() : $args[1];
            
            $playerManager->removeGroup($groupManager->getGroup($args[6]), true, $levelName);
        
            $sender->sendMessage(Main::format("User §2{$nick} §7removed from group ”§2{$args[4]}§7” on world §2{$levelName}"));
           break;
           default:
			 	       $sender->sendMessage(Main::getErrorMessage());
          }
         break;
   
         default:
			       $sender->sendMessage(Main::getErrorMessage());
      }
    	break;
    	
    	case "list":
    	 $sender->sendMessage("§7{$args[1]}‘s permissions:");
    	 foreach($groupManager->getPlayer($args[1])->getPermissions() as $permission)
    	  $sender->sendMessage(" §7{$permission}");
    	break;
    	
    	case "delete":
    	 $groupManager->getPlayer($args[1])->delete();
    	 $sender->sendMessage("User §2{$args[1]} §7deleted!");
    	break;
    	
    	case "add":
    	 if(!isset($args[3])) {
    	 	$sender->sendMessage(Main::format("Usage: /pex user $args[1] add (permission) {time[s/m/h/d]}"));
    	 	return;
    	 }
    	 $time = null;
        
      if(isset($args[4])) {
  	    if(strpos($args[4], "d"))
		      $time = intval(explode("d", $args[4])[0]) * 86400;
          
       if(strpos($args[4], "h"))
	 	     $time = intval(explode("h", $args[4])[0]) * 3600;

	   	  if(strpos($args[4], "m"))
		      $time = intval(explode("h", $args[4])[0]) * 60;

	 	    if(strpos($args[4], "s"))
	 	     $time = intval(explode("s", $args[4])[0]);
	 	    $playerManager = $groupManager->getPlayer($args[1])->addPermission($args[3], $time);
      } else
       $playerManager = $groupManager->getPlayer($args[1])->addPermission($args[3]);
    	 
    	 $sender->sendMessage(Main::format("Permission ”§2{$args[3]}§7” added!".($time == null ? "" : " for §2{$args[4]}")));
    	break;
    	
    	case "remove":
    	 if(!isset($args[3])) {
    	 	$sender->sendMessage(Main::format("Usage: /pex user $args[1] remove (permission)"));
    	 	return;
    	 }
    	 
    	 $playerManager = $groupManager->getPlayer($args[1])->removePermission($args[3]);
    	 $sender->sendMessage(Main::format("Permission ”§2{$args[3]}§7” removed!"));
    	break;
    	
    	case "group":
      if(!isset($args[4])) {
       $sender->sendMessage(Main::getErrorMessage());
       return;
      }
      
      if(!$groupManager->isGroupExists($args[4])) {
       $sender->sendMessage(Main::format("This group does not exists!"));
       return;
      }
      
      switch($args[3]) {
      	case "add":  
        $playerManager = $groupManager->getPlayer($args[1]);
        
        $player = $playerManager->getPlayer();
        $nick = $player instanceof Player ? $player->getName() : $args[1];
        
        $time = null;
        
        if(isset($args[5])) {
  	      if(strpos($args[5], "d"))
		        $time = intval(explode("d", $args[5])[0]) * 86400;
          
        	if(strpos($args[5], "h"))
	 	       $time = intval(explode("h", $args[5])[0]) * 3600;

	       	if(strpos($args[5], "m"))
		        $time = intval(explode("h", $args[5])[0]) * 60;

	 	      if(strpos($args[5], "s"))
	 	       $time = intval(explode("s", $args[5])[0]);
	 	      $playerManager->addGroup($groupManager->getGroup($args[4]), $time);
        } else
         $playerManager->addGroup($groupManager->getGroup($args[4]));
        
        $sender->sendMessage(Main::format("User §2{$nick} §7added to group ”§2{$args[4]}§7”".($time == null ? "" : " for §2{$args[5]}")));
   break;
   
       case "remove":      
        $playerManager = $groupManager->getPlayer($args[1]);
        
        $player = $playerManager->getPlayer();
        $nick = $player instanceof Player ? $player->getName() : $args[1];
        
        $playerManager->removeGroup($groupManager->getGroup($args[4]));
        
        $sender->sendMessage(Main::format("User §2{$nick} §7removed from group ”§2{$args[4]}§7”"));
   break;
   
   case "set":
    $playerManager = $groupManager->getPlayer($args[1]);
    
    $player = $playerManager->getPlayer();
    $nick = $player instanceof Player ? $player->getName() : $args[1];
    
    $time = null;
        
    if(isset($args[5])) {
  	  if(strpos($args[5], "d"))
		    $time = intval(explode("d", $args[5])[0]) * 86400;
          
     if(strpos($args[5], "h"))
	 	   $time = intval(explode("h", $args[5])[0]) * 3600;

	   	if(strpos($args[5], "m"))
		    $time = intval(explode("h", $args[5])[0]) * 60;

	 	  if(strpos($args[5], "s"))
	 	   $time = intval(explode("s", $args[5])[0]);
	 	  $playerManager->setGroup($groupManager->getGroup($args[4]), $time);
    } else
     $playerManager->setGroup($groupManager->getGroup($args[4]));
        
    $sender->sendMessage(Main::format("{$nick}’s group set to ”§2{$args[4]}§7”".($time == null ? "" : " for §2{$args[5]}")));
     
     break;
     default:
			 	 $sender->sendMessage(Main::getErrorMessage());
    }
   break;
   
   default:
			 $sender->sendMessage(Main::getErrorMessage());
  }
 break;
 default:
		$sender->sendMessage(Main::getErrorMessage());
		}
	}
}