<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;
use Core\api\LobbyAPI;

class LobbyCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("lobby", "Komenda lobby", true);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
		if(!$this->canUse($sender))
		    return;

		if(empty($args)) {
			$sender->sendMessage(Main::formatLines(["Poprawne uzycie:", "/lobby §8(§4on §7| §4off§8)", "/lobby settime §8(§4D§7:§4M§7:§4Y§8) (§4H§7:§4M§8)", "/lobby add §8(§4nick§8)", "/lobby remove §8(§4nick8)", "/lobby list"]));
			return;
		}
		
		switch($args[0]) {
			case "add":
			 if(!isset($args[1])) {
			 	$sender->sendMessage(Main::format("Poprawne uzycie: /lobby add (§4nick§8)"));
			 	return;
			 }
			 LobbyAPI::addPlayer($args[1]);
			 
			 $sender->sendMessage(Main::format("Dodano do lobby gracza §4{$args[1]}"));
			break;
			
			case "remove":
			 if(!isset($args[1])) {
			 	$sender->sendMessage(Main::format("Poprawne uzycie: /lobby remove (§4nick§8)"));
			 	return;
			 }
			 LobbyAPI::removePlayer($args[1]);
			 
			 $sender->sendMessage(Main::format("Usunieto z lobby gracza §4{$args[1]}"));
			break;
			
			case "list":
			 $sender->sendMessage(Main::format("Lista graczy: §4".implode("§8, §4", LobbyAPI::getLobbyPlayers())));
			break;
			
			case "on":
			 LobbyAPI::setLobby(true);
			 $sender->sendMessage(Main::format("Pomyslnie wlaczono lobby"));
			break;
			
			case "off":
			 LobbyAPI::setLobby(false);
			 $sender->sendMessage(Main::format("Pomyslnie wylaczono lobby"));
			break;
			
			case "settime":
			 if(!isset($args[2])) {
			 	$sender->sendMessage(Main::format("Poprawne uzycie: /lobby settime §8(§4D§7.§4M§7.§4Y§8) (§4H§7:§4M§8)"));
			 	return;
			 }
			 
			 $hm = explode(':', $args[2]);
			 
			 if(!is_numeric($hm[0]) || !is_numeric($hm[1]) || $hm[0] > 24 || $hm[1] > 59) {
			 	$sender->sendMessage(Main::format("Nieprawidlowy format godziny!"));
			 	return;
			 }
			 
			 $date = $args[1] . " " . $args[2];
			 
			 if(time() > strtotime($date)) {
			 	$sender->sendMessage(Main::format("Nieprawidlowa data!"));
			 	return;
			 }
			 
			 LobbyAPI::setLobbyDate($date);
			 $sender->sendMessage(Main::format("Pomyslnie ustawiono date wylaczenia lobby na §4$date"));
			break;
			
			default:
                $sender->sendMessage(Main::formatLines(["Poprawne uzycie:", "/lobby §8(§4on §7| §4off§8)", "/lobby settime §8(§4D§7:§4M§7:§4Y§8) (§4H§7:§4M§8)", "/lobby add §8(§4nick§8)", "/lobby remove §8(§4nick8)", "/lobby list"]));
        }
	}
}