<?php

namespace Core\commands;

use pocketmine\Player;

use pocketmine\command\{
	Command, CommandSender
};

use pocketmine\math\Vector3;

use Core\Main;

class SprawdzanieCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("sprawdzanie", "Komenda sprawdzanie", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		if(!$sender instanceof Player) {
			$sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
			return;
		}

		if(!isset($args[0])) {
			$sender->sendMessage(Main::formatLines(["Poprawne uzycie:", "/sprawdzanie sprawdz §8(§4nick§8)", "/sprawdzanie zbanuj §8(§4nick§8)", "/sprawdzanie czysty §8(§4nick§8)", "/sprawdzanie ustaw"]));
			return;
		}

		switch($args[0]) {

			case "sprawdz":

			 if(!isset($args[1])) {
			 	$sender->sendMessage(Main::format("Poprawne uzycie: /sprawdzanie sprawdz §8(§4nick§8)"));
			 	return;
			 }

			 $player = $sender->getServer()->getPlayer($args[1]);

			 if($player == null) {
			 	$sender->sendMessage(Main::format("Ten gracz jest §4offline"));
			 	return;
			 }

			 $nick = $player->getName();

			 if($nick == $sender->getName()) {
			 	$sender->sendMessage(Main::format("Nie mozesz sprawdzic samego siebie!"));
			 	return;
			 }

			 if(isset(Main::$spr[$nick])) {
			 	$sender->sendMessage(Main::format("Ten gracz jest juz sprawdzany!"));
			 	return;
			 }

			 Main::$spr[$nick] = [$player->asVector3(), $sender->getName()];

			 $cfg = Main::getInstance()->getConfig()->get("sprawdzanie");

			 if(!$cfg) {
			     $sender->sendMessage(Main::format("Musisz ustawic pozycje sprawdzania!"));
			     return;
             }

			 $pos = new Vector3($cfg['x'], $cfg['y'], $cfg['z']);

			 $sender->teleport($pos);
			 $player->teleport($pos);

			 $sender->getServer()->broadcastMessage(Main::format("Gracz §4$nick §7zostal wezwany do sprawdzania przez administratora §4{$sender->getName()}§7!"));

			 $sender->sendMessage(Main::format("Pomyslnie wezwano do sprawdzania gracza §4$nick"));
			 $player->sendMessage(Main::formatLines(["Zostales wezwany do sprawdzania!", "Nick administratora: §4{$sender->getName()}", "Mozesz uzywac komend §4/msg§7, §4/r§7!"]));

			break;

			case "zbanuj":

			 if(!isset($args[1])) {
			 	$sender->sendMessage(Main::format("Poprawne uzycie: /sprawdzanie zbanuj §8(§4nick§8)"));
			 	return;
			 }

			 $player = $sender->getServer()->getPlayer($args[1]);

			 if($player == null) {
			 	$sender->sendMessage(Main::format("Ten gracz jest §4offline"));
			 	return;
			 }

			 $nick = $player->getName();

			 if(!isset(Main::$spr[$nick])) {
			 	$sender->sendMessage(Main::format("Ten gracz nie jest sprawdzany!"));
			 	return;
			 }

			 $api = Main::getInstance()->getBanAPI();

			 $api->setBan($nick, "Cheaty", Main::$spr[$nick][1]);

			 $player->teleport($player->getLevel()->getSafeSpawn());

			 unset(Main::$spr[$nick]);

			 $player->kick($api->getBanMessage($player), false);

			 $sender->teleport($sender->getLevel()->getSafeSpawn());

			 $sender->getServer()->broadcastMessage(Main::format("Gracz §4$nick §7zostal zbanowany za §4cheaty§7!"));

			break;

			case "czysty":

			 if(!isset($args[1])) {
			 	$sender->sendMessage(Main::format("Poprawne uzycie: /sprawdzanie czysty §8(§4nick§8)"));
			 	return;
			 }

			 $player = $sender->getServer()->getPlayer($args[1]);

			 if($player == null) {
			 	$sender->sendMessage(Main::format("Ten gracz jest §4offline"));
			 	return;
			 }

			 $nick = $player->getName();

			 if(!isset(Main::$spr[$nick])) {
			 	$sender->sendMessage(Main::format("Ten gracz nie jest sprawdzany!"));
			 	return;
			 }

			 $player->teleport(Main::$spr[$nick][0]);
			 $sender->teleport($player->getLevel()->getSafeSpawn());

			 unset(Main::$spr[$nick]);

			 $sender->getServer()->broadcastMessage(Main::format("Gracz §4$nick §7okazal sie byc czysty!"));

			break;

			case "ustaw":
			 $pos = $sender->asVector3();

			 $x = $pos->getX();
			 $y = $pos->getY();
			 $z = $pos->getZ();

			 $cfg = Main::getInstance()->getConfig();

			 $cfg->set("sprawdzanie", [
			  "x" => $x,
			  "y" => $y,
			  "z" => $z
			 ]);
			 $cfg->save();

			 $sender->sendMessage(Main::format("Pomyslnie ustawiono pozycje sprawdzarki!"));
			break;

			default:
			 $sender->sendMessage(Main::format("Poprawne uzycie: /sprawdzanie czysty §8(§4nick§8)"));
		}
	}
}
