<?php

declare(strict_types=1);

namespace permissionex\group;

use pocketmine\{
	Server, Player, IPlayer, OfflinePlayer
};
use pocketmine\permission\PermissionAttachment;
use permissionex\Main;
use permissionex\provider\Provider;
use permissionex\events\player\PlayerUpdateGroupEvent;

class PlayerGroupManager {
	
	private $player;
	private $attachment = null;
	
	public function __construct(IPlayer $player, Provider $provider) {
		$this->player = $player;
		$this->provider = $provider;
		$this->init();
	}
	
	private function init() : void {
		if($this->player instanceof Player)
		 $this->attachment = $this->player->addAttachment(Main::getInstance());
	}
	
	public function getAttachment() : PermissionAttachment {
		return $this->attachment;
	}
	
	public function getPlayer() : IPlayer {
		return $this->player;
	}
 
 public function getGroups() : array {
 	return $this->provider->getPlayerGroups($this->player);
	}
	
	public function addGroup(Group $group, ?int $time = null, ?string $levelName = null) : void {
		if($this->hasGroup($group, false))
		 $this->removeGroup($group, false);
		if($time != null) {
			$date = date('d.m.Y H:i:s', strtotime(date("H:i:s")) + $time);
 	$this->provider->addPlayerGroup($this->player, $group, $date, $levelName);
		} else
			$this->provider->addPlayerGroup($this->player, $group, null, $levelName);
		
		$this->updatePermissions();
		
		if($this->player instanceof Player)
		 (new PlayerUpdateGroupEvent($this->player))->call();
	}
	
	public function addDefaultGroup() : void {
		$defaultGroup = Main::getInstance()->getGroupManager()->getDefaultGroup();
  $this->addGroup($defaultGroup);
 }
	
	public function setGroup(Group $group, ?int $time = null, ?string $levelName = null) : void {
		$this->removeGroups($levelName);
  $this->addGroup($group, $time, $levelName);
 }
 
 public function removeGroup(Group $group, bool $addDefault = true, ?string $levelName = null) : void {
 	$this->provider->removePlayerGroup($this->player, $group, $levelName);
 	
 	if($addDefault && $this->getGroup() == null)
 	 $this->addDefaultGroup();
 	
 	$this->updatePermissions();
 }
 
 public function removeGroups(?string $levelName = null) : void {
 	$this->provider->removePlayerGroups($this->player, $levelName);
 	$this->updatePermissions();
 }
 
 public function hasGroup(?Group $group = null, bool $checkLevel = true) : bool {
		return $this->provider->hasPlayerGroup($this->player, $group, $checkLevel);
 }
 
 // RETURN FIRST GROUP IN HIERARCHY
 public function getGroup() : ?Group {
 	$groups = [];
 	
 	foreach(Main::getInstance()->getGroupManager()->getAllGroups() as $group)
 		if($this->hasGroup($group)) {
 			$rank = $group->getRank() == null ? 0 : $group->getRank();
 		 $groups[$rank][] = $group;
 		}
 	
 	if(empty($groups))
 	 return null;
 		
 	return $groups[max(array_keys($groups))][0];
 }
 
 public function getGroupExpiry(Group $group) :?int {
 	$date = $this->provider->getPlayerGroupExpiryDate($this->player, $group);
 	
 	if($date == null)
 	 return null;
 	
 	return strtotime($date) - time();
 }
 
 public function getPermissions() : array {
 	$permissions = [];
 	
 	foreach($this->getGroups() as $group) {
   foreach($group->getPermissions() as $permissionName) {
   	$permission = Server::getInstance()->getPluginManager()->getPermission($permissionName);
   	
   	if($permissionName == '*') {
   		if(!in_array($permissionName, $permissions))
   		  $permissions[] = $permissionName;
   		
   		foreach(Server::getInstance()->getPluginManager()->getPermissions() as $perm)
   		 if(!in_array($perm->getName(), $permissions))
       $permissions[] = $perm->getName();
       
      // PERMISSION.*
   	} elseif(substr($permissionName, -1) == '*') {
   		 if(!in_array($permissionName, $permissions))
   		  $permissions[] = $permissionName;
   		foreach(Server::getInstance()->getPluginManager()->getPermissions() as $perm)
   		 if(substr($perm->getName(), 0, strlen($permissionName)-1) == substr($permissionName, 0, strlen($permissionName)-1))
   		  $permissions[] = $perm->getName();
   	} else {
   		if(!in_array($permissionName, $permissions))
   		 $permissions[] = $permissionName;
   		
   		if($permission == null)
   		 continue;
   		 
   		foreach($permission->getChildren() as $childPerm => $value)
   		 if(!in_array($childPerm, $permissions))
   		  $permissions[] = $childPerm;
   	}
   }
  }
  
  foreach($this->provider->getPlayerPermissions($this->player) as $permissionName) {
  	$permission = Server::getInstance()->getPluginManager()->getPermission($permissionName);
  	if($permissionName == '*') {
   	foreach(Server::getInstance()->getPluginManager()->getPermissions() as $perm)
   	 if(!in_array($perm->getName(), $permissions))
      $permissions[] = $perm->getName();
      
      // PERMISSSION.*
   	} elseif(substr($permissionName, -1) == '*') {
   		 if(!in_array($permissionName, $permissions))
   		  $permissions[] = $permissionName;
   		foreach(Server::getInstance()->getPluginManager()->getPermissions() as $perm)
   		 if(substr($perm->getName(), 0, strlen($permissionName)-1) == substr($permissionName, 0, strlen($permissionName)-1))
   		  $permissions[] = $perm->getName();
   	} else {
   		if(!in_array($permissionName, $permissions))
   		 $permissions[] = $permissionName;
   		
   		if($permission == null)
   		 continue;
   		
   		foreach($permission->getChildren() as $childPerm => $value)
   		 if(!in_array($childPerm, $permissions))
   		  $permissions[] = $childPerm;
  	}
  }
  
  return $permissions;
 }
 
 public function addPermission(string $permission, ?int $time = null) : void {
 	if($this->hasPermission($permission))
 	 $this->removePermission($permission);
 	
 	if($time != null) {
 		$date = date('d.m.Y H:i:s', strtotime(date("H:i:s")) + $time);
 		$this->provider->addPlayerPermission($this->player, $permission, $date);
 	} else
   $this->provider->addPlayerPermission($this->player, $permission);
   
 	$this->updatePermissions();
 }
 
 public function removePermission(string $permission) : void {
 	$this->provider->removePlayerPermission($this->player, $permission);
 	$this->updatePermissions();
 }
 
 public function hasPermission(string $permission) : bool {
 	return $this->provider->hasPlayerPermission($this->player, $permission);
 }
 
 public function delete() : void {
 	$this->provider->deleteUser($this->player);
 	$this->updatePermissions();
 }
 
 public function updatePermissions() : void {
 	$player = $this->player;
 	
 	if($player instanceof OfflinePlayer)
   return;
  
  $permissions = [];
  
  foreach($this->getPermissions() as $permission)
  	$permissions[$permission] = true;
  
  $this->attachment->clearPermissions();
  $this->attachment->setPermissions($permissions);
 }
}