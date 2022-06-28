<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class ApvpCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("apvp", "Komenda apvp", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender)) return;

		if(empty($args)) {
			$sender->sendMessage(Main::formatLines(["Poprawne uzycie:", "/apvp knockback §8(§4default §7| §4ile§8)", "/apvp attackdelay §8(§4default §7| §4ile§8)"]));
			return;
		}
		
		switch($args[0]) {
			case "knockback":
			 if(!isset($args[1])) {
			 	$sender->sendMessage(Main::format("Poprawne uzycie: /apvp knockback §8(§4default §7| §4ile§8)"));
			 	return;
			 }
			 
			 if($args[1] !== "default" && !is_numeric($args[1])) {
			 	$sender->sendMessage(Main::format("Argument §41 §7musi byc numeryczny!"));
			 	return;
			 }
			 
			 $cfg = Main::getInstance()->getConfig();
			 
			 if($args[1] == "default") {
			 	$cfg->remove("knockback");
			 	$cfg->save();
			 	
			 	$sender->sendMessage(Main::format("Knockback zostal §4zresetowany"));
			 	return;
			 }
			 
			 $cfg->set("knockback", $args[1]);
			 $cfg->save();
			 
			 $sender->sendMessage(Main::format("Knockback zostal ustawiony na §4$args[1]"));
			break;
			
			case "attackdelay":
			 if(!isset($args[1])) {
			 	$sender->sendMessage(Main::format("Poprawne uzycie: /apvp attackdelay §8(§4default §7| §4ile§8)"));
			 	return;
			 }
			 
			 if($args[1] !== "default" && !is_numeric($args[1])) {
			 	$sender->sendMessage(Main::format("Argument §41 §7musi byc numeryczny!"));
			 	return;
			 }
			 
			 $cfg = Main::getInstance()->getConfig();
			 
			 if($args[1] == "default") {
			 	$cfg->remove("attackdelay");
			 	$cfg->save();
			 	
			 	$sender->sendMessage(Main::format("Attack delay zostal §4zresetowany"));
			 	return;
			 }
			 
			 $cfg->set("attackdelay", $args[1]);
			 $cfg->save();
			 
			 $sender->sendMessage(Main::format("Attack delay zostal ustawiony na §4$args[1]"));
			break;
			default:
			 $sender->sendMessage(Main::format("Nieznany argument!"));
		}
	}
}