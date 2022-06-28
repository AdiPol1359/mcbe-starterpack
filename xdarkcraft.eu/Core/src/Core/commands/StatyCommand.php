<?php

namespace Core\commands;

use pocketmine\Server;

use pocketmine\command\{
	Command, CommandSender
};

use Core\Main;

class StatyCommand extends CoreCommand {

	public function __construct() {
		parent::__construct("staty", "Komenda staty", false, ["gracz"]);
	}

	public function execute(CommandSender $sender, string $label, array $args) : void {
	    if(!$this->canUse($sender))
	        return;

		$nick = $sender->getName();
		
		if(isset($args[0]))
		 $nick = $args[0];
		 
		$api = Main::getInstance()->getStatsAPI();
		
		if(!$api->isInDatabase($nick)) {
			$sender->sendMessage(Main::format("Nie znaleziono tego gracza w bazie danych!"));
			return;
		}

		$g_api = $sender->getServer()->getPluginManager()->getPlugin("Gildie");
		$g_format = "§4BRAK";

		if($g_api != null) {
		    if($g_api->getGuildManager()->isInGuild($nick)) {
		        $g = $g_api->getGuildManager()->getPlayerGuild($nick);

                $g_format = "§8[§4{$g->getTag()}§8] - §4{$g->getName()}";
            }
        }

		$sender->sendMessage(Main::formatLines([
		 "Nick: §4$nick",
		 "Gildia: $g_format",
		 "Punkty: §4".Main::getInstance()->getPointsAPI()->getPoints($nick),
		 "Zabojstwa: §4".$api->getKills($nick),
		 "Smierci: §4".$api->getDeaths($nick),
		 "Zjedzone koxy: §4".$api->getKoxy($nick),
		 "Zjedzone refy: §4".$api->getRefy($nick),
		 "Rzucone perly: §4".$api->getPerly($nick)
		]));
 }
}