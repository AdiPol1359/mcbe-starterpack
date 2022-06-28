<?php

declare(strict_types=1);

namespace permissionex\group;

use pocketmine\{
	Server, Player
};
use pocketmine\utils\Config;
use permissionex\Main;
use permissionex\provider\Provider;

class GroupManager {
	
	private $provider;
	private $config;
 private $players = [];
 
 public static function expiryFormat(?int $time = null) : ?array {
 	if($time == null)
 	 return null;
 	
		$days = intval(intval($time) / (3600*24)); 
		$hours = (intval($time) / 3600) % 24;
		$minutes = (intval($time) / 60) % 60;
		$seconds = intval($time) % 60;
		
		if($hours < 10)
		 $hours = "0".$hours;
		
		if($minutes < 10)
		 $minutes = "0".$minutes;
		
		if($seconds < 10)
		 $seconds = "0".$seconds;
		
		return [
		 "seconds" => $seconds,
		 "minutes" => $minutes,
		 "hours" => $hours,
		 "days" => $days
		];
	}
 
 public function __construct(Provider $provider) {
 	$this->provider = $provider;
  $this->init();
 }
 
 private function init() : void {
 	Main::getInstance()->saveResource("groups.yml");
 	
 	$this->config = new Config(Main::getInstance()->getDataFolder(). 'groups.yml', Config::YAML);
 }
 
 public function getProvider() : Provider {
 	return $this->provider;
 }
 
 public function getConfig() : Config {
  return $this->config;
 }
 
 public function getPlayer(string $playerName) : PlayerGroupManager {
 	$p = Server::getInstance()->getPlayer($playerName);
 	if($p instanceof Player)	
  $player = Server::getInstance()->getPlayer($playerName);
  else
  $player = Server::getInstance()->getOfflinePlayer($playerName);
  
  if($player instanceof Player && isset($this->players[$player->getName()]))
   return $this->players[$player->getName()];
  
  return new PlayerGroupManager($player, $this->provider);
 }
 
 public function registerPlayer(Player $player) : void {
 	if(!isset($this->players[$player->getName()]))
 	 $this->players[$player->getName()] = new PlayerGroupManager($player, $this->provider);
 	
 	$this->players[$player->getName()]->updatePermissions();
 }
 
 public function unregisterPlayer(Player $player) : void {
 	if(isset($this->players[$player->getName()])) {
 		$player->removeAttachment($this->getPlayer($player->getName())->getAttachment());
 		unset($this->players[$player->getName()]);
 	}
 }

 public function getDefaultGroup() : ?Group {
 	foreach($this->config->get("groups") as $groupName => $groupData)
 	 if(isset($groupData['default']) && $groupData['default'])
 	  return $this->getGroup($groupName);
 	
 	return null;
 }
 
 public function setDefaultGroup(Group $defGroup) : void {
 	$groups = [];
 	
 	foreach($this->getAllGroups() as $group) {
 		$groupData = $group->getData();
 		
 		if(isset($groupData['default']) && $groupData['default'])
 		 if($group->getName() != $defGroup->getName())
 		  $groupData['default'] = false;
 		
 		if($group->getName() == $defGroup->getName())
 		 $groupData['default'] = true;
 		
 		$group->setData($groupData);
 	}
 }
 
 public function getGroup(string $groupName) : ?Group {
 	if(!isset($this->config->get("groups")[$groupName]))
 	 return null;
 	
 	return new Group($groupName, $this->provider, $this->config->get("groups")[$groupName]);
 }
 
 // RETURN RANKS FROM HIGHEST TO LOWEST HIERARCHY
 public function getAllGroups() : array {
 	$groups = [];
 	
 	foreach($this->config->get("groups") as $groupName => $groupData)
 	 $groups[] = $this->getGroup($groupName);
 	
 	$groupsHierarchy = [];
 	
 	foreach($groups as $group) {
 		$rank = $group->getRank() == null ? 0 : $group->getRank();
 	 $groupsHierarchy[$rank][] = $group;
 	}
 	
 	krsort($groupsHierarchy);
 	
 	$groups = [];
 	
 	foreach($groupsHierarchy as $grps)
 	 foreach($grps as $group)
 	  $groups[] = $group;
 	
 	return $groups;
 }
 
 public function getAllUsers() : array {
  $users = [];
  
  foreach($this->provider->getAllUsers() as $nick)
   $users[] = $this->getPlayer($nick);
  
  return $users;
 }
 
 public function isGroupExists(string $groupName) : bool {
 	return $this->getGroup($groupName) !== null;
 }
 
 public function userExists(string $userName) : bool {
 	return $this->provider->userExists($userName);
 }
 
 public function createGroup(string $groupName, array $parents = []) : void {
 	$groups = $this->config->get("groups");
 	$groups[$groupName] = [
 	 "rank" => 0,
 	 "parents" => $parents,
 	 "permissions" => []
 	];
 	
 	$this->config->set("groups", $groups);
  $this->config->save();
 }
 
 public function reload() : void {
 	$this->config = new Config(Main::getInstance()->getDataFolder(). 'groups.yml', Config::YAML);
 }
}