<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};

use Gildie\Main;

class InfoCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("info", "Komenda info");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->canUse($sender))
            return;

        $guildManager = Main::getInstance()->getGuildManager();

        if(empty($args)) {
            if(!$guildManager->isInGuild($sender->getName())) {
                $sender->sendMessage(Main::format("Nie masz gildii!"));
                return;
            }

            $guild = $guildManager->getPlayerGuild($sender->getName());
        }

        if(isset($args[0])) {
            if(!$guildManager->isGuildExists($args[0])) {
                $sender->sendMessage(Main::format("Ta gildia nie istnieje!"));
                return;
            }

            $guild = $guildManager->getGuildByTag($args[0]);
        }

        $tag = $guild->getTag();
        $name = $guild->getName();
        $leader = $guild->getLeader();
        $lifes = $guild->getLifes();
        $expiryDate = $guild->getExpiryDate();
        $points = $guild->getPoints();
        $rankPlace = $guild->getRankPlace();

        $expiryH = 0;
        $expiryM = 0;
        $expiryS = 0;

        if(!$guild->canConquer()) {
            $exipiryTime = strtotime($guild->getConquerDate()) - time();

            $expiryH = floor($exipiryTime / 3600);
            $expiryM = floor(($exipiryTime / 60) % 60);
            $expiryS = $exipiryTime % 60;
        }

        $members = "";

        foreach($guild->getPlayers() as $nick) {
            $Fnick = $nick;

            if($nick === $guild->getLeader() || $nick === $guild->getOfficer())
                $Fnick = "§l".$Fnick;

            if($sender->getServer()->getPlayer($nick))
                $members .= "§4".$Fnick."§r§8, ";
            else
                $members .= "§c".$Fnick."§8, ";
        }

        $members = substr($members, 0, strlen($members) - 2);

        $alliances = "";

        foreach($guild->getAlliances() as $tag) {
            $aGuild = $guildManager->getGuildByTag($tag);

            $alliances .= "§8[§4{$aGuild->getTag()}§8] §4{$aGuild->getName()}§7, ";
        }

        if($alliances === "")
            $alliances = "§4BRAK";
        else
            $alliances = substr($alliances, 0, strlen($alliances) - 2);

        $sender->sendMessage(" \n§8          §4§lPolishHard§7.EU\n\n");
        $sender->sendMessage("§8§l>§r §7Tag: §8[§4{$tag}§8]");
        $sender->sendMessage("§8§l>§r §7Nazwa:§4 $name");
        $sender->sendMessage("§8§l>§r §7Zalozyciel:§4 $leader");
        $sender->sendMessage("§8§l>§r §7Ranking: §4$points");
        $sender->sendMessage("§8§l>§r §7Miejsce w rankingu: §4$rankPlace");
        $sender->sendMessage("§8§l>§r §7Zycia: §4$lifes");
        $sender->sendMessage("§8§l>§r §7Mozna podbic za: §4$expiryH §7godzin, §4$expiryM §7minut i §4$expiryS §7sekund");
        $sender->sendMessage("§8§l>§r §7Waznosc: §4$expiryDate");
        $sender->sendMessage("§8§l>§r §7Czlonkowie: $members");
        $sender->sendMessage("§8§l>§r §7Sojusze: $alliances");
        $sender->sendMessage(" \n");
    }
}