<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\Main;

class GCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("g", "Komenda g");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->canUse($sender))
            return;

    	if(empty($args) || !isset($args[0]) || (isset($args[0]) && !in_array($args[0], ["top", "topka"]))) {
        $sender->sendMessage(" \n§8          §4§lPolishHard§7.EU\n\n");

        $sender->sendMessage("§8§l>§r §4/ustawbaze §8- §7Ustawia miejsce teleportacji do bazy");

        $sender->sendMessage("§8§l>§r §4/dolacz [tag] §8- §7Przyjmuje zaproszenie do gildii");

        $sender->sendMessage("§8§l>§r §4/zapros [gracz] §8- §7Zaprasza gracza do gildii");

        $sender->sendMessage("§8§l>§r §4/lider [gracz] §8- §7Oddaje zalozyciela gildii");

        $sender->sendMessage("§8§l>§r §4/zastepca [gracz] §8- §7Nadaje zastepce gildii");

        $sender->sendMessage("§8§l>§r §4/wyrzuc [gracz] §8- §7Wyrzuca gracza z gildii");

        $sender->sendMessage("§8§l>§r §4/info [tag] §8- §7Informacje o danej gildii");

        $sender->sendMessage("§8§l>§r §4/zaloz [tag] [nazwa] §8- §7Tworzy gildie");

        $sender->sendMessage("§8§l>§r §4/przedluz §8- §7Przedluza waznosc gildii");

        $sender->sendMessage("§8§l>§r §4/sojusz [tag] §8- §7Sojusz gildyjny");

        $sender->sendMessage("§8§l>§r §4/ustawbaze §8- §7Ustawia miejsce teleportacji do bazy");

        $sender->sendMessage("§8§l>§r §4/rozwiaz [tag] §8- §7Usuwa sojusz gildyjny");

        $sender->sendMessage("§8§l>§r §4/baza §8- §7Teleportuje do bazy gildii");
		
		$sender->sendMessage("§8§l>§r §4/skarbiec §8- §7Otwiera skarbiec gildyjny");

		$sender->sendMessage("§8§l>§r §4/walka §8- §7Zaprasza innych graczy na walke");

        $sender->sendMessage("§8§l>§r §4/powieksz §8- §7Powieksza teren gildii");

        $sender->sendMessage("§8§l>§r §4/opusc §8- §7Opuszcza gildie");

        $sender->sendMessage("§8§l>§r §4/usun §8- §7Usuwa gildie");

        $sender->sendMessage("§8§l>§r §4/permisje §8- §7Permisje gildii");

        $sender->sendMessage("§8§l>§r §4/itemy §8- §7Lista przedmiotow potrzebnych na gildie");

        $sender->sendMessage(" ");

        $sender->sendMessage("§8§l>§r §4! §8- §7Chat gildyjny");
        $sender->sendMessage("§8§l>§r §4!! §8- §7Chat sojuszniczy");
        $sender->sendMessage("§8§l>§r §4# §8- §7Wysyla do calej gildii, ze potrzebujesz pomocy");

        $sender->sendMessage(" \n");
        return;
       }
       
       if($args[0] == "top" || $args[0] == "topka") {
       	$guildManager = Main::getInstance()->getGuildManager();
        $top = $guildManager->getGuildsTop();
        
        $sender->sendMessage(" \n§8          §4§lPolishHard§7.EU\n\n");
        for($i = 1; $i <= 10; $i++) {
        	if(isset($top[$i]))
     	    $sender->sendMessage("§8§l>§r §7{$i}. {$top[$i]->getTag()}: §4{$top[$i]->getPoints()}");
     	   else
          $sender->sendMessage("§8§l>§r §7{$i}. BRAK");
        }
        $sender->sendMessage(" \n");
    }
    }
}