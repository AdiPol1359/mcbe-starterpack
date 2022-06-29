<?php

namespace core\command\commands;

use core\caveblock\CaveManager;
use core\command\BaseCommand;
use core\manager\managers\StatsManager;
use core\user\UserManager;
use core\util\utils\MessageUtil;
use core\util\utils\TimeUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Server;

class PlayerCommand extends BaseCommand{
    public function __construct() {
        parent::__construct("player", "Player Command", false, true, "Komenda player sluzy do pokazywania informacji o wskazanym graczu", ["gracz", "staty", "stats"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        $selectedPlayer = null;

        if(empty($args))
            $selectedPlayer = $player->getName();

        if(!$selectedPlayer)
            $selectedPlayer = implode(" ", $args);

        $user = UserManager::getUser($selectedPlayer);

        if(!$user){
            $player->sendMessage(MessageUtil::format("Ten gracz nigdy sie nie logowal na serwerze!"));
            return;
        }

        $this->getServer()->getPlayerExact($user->getName()) ? $status = "§aONLINE" : $status = "§cOFFLINE";

        if(count(CaveManager::getCaves($user->getName())) <= 0)
            $caves = "BRAK";
        else{

            $playerCaves = [];

            foreach(CaveManager::getCaves($user->getName()) as $cave)
                $playerCaves[] = $cave->getName();

            $caves = implode("§7, §9", $playerCaves);
        }

        $money = $user->getPlayerMoney();

        $cobblestone = $user->getCobble();
        $quests = $user->getDoneQuestCount();
        $kills = $user->getStat(StatsManager::KILLS);
        $deaths = $user->getStat(StatsManager::DEATHS);
        $assists = $user->getStat(StatsManager::ASSISTS);
        $lastPlayed = Server::getInstance()->getPlayer($user->getName()) ? time() : $user->getStat(StatsManager::LAST_PLAYED);
        $timePlayed = ($user->getStat(StatsManager::TIME_PLAYED) + ($user->getStat(StatsManager::TIME_PLAYED) + (Server::getInstance()->getPlayerExact($user->getName()) ? (time() - $user->getStat(StatsManager::LAST_PLAYED)) : 0)));

        $player->sendMessage(MessageUtil::customFormat([
            "Status: ".$status,
            "Stan konta: §9".$money."§r§8zl",
            "Jaskinie gracza: §9".$caves,
            "Wykopany cobblestone: §9".$cobblestone,
            "Wykonanych questow: §9".$quests,
            "Zabojstw: §9".$kills,
            "Smierci: §9".$deaths,
            "Asyst: §9".$assists,
            "Ostatnio widziany: §9".gmdate("d.m.Y H:i:s", $lastPlayed),
            "Spedzony czas: §9".TimeUtil::convertIntToStringTime($timePlayed, "§9", "§7", true, false)
        ], "§r§7GRACZ §l§9".$user->getName()."§r§7!"));
    }
}