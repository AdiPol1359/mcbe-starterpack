<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class RankCommand extends BaseCommand{
    public function __construct(){
        parent::__construct("rank", "Rank Command", false, true, "Po wpisaniu komendy wyswietlaja sie informacje o danej randze, jej przywileja oraz wymgania", ["rangi", "ranga"]);

        $parameters = [
            0 => [
                $this->commandParameter("rankOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "rankOptions", ["vip", "svip", "sponsor", "yt", "yt+"])
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        if(empty($args)){
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["vip", "svip", "sponsor", "yt"]]));
            return;
        }

        switch($args[0]){
            case "vip":
                $player->sendMessage(MessageUtil::formatLines([
                    "§l§9KOMENDY:",
                    "/feed",
                    "/enchant",
                    "§l§9INNE:",
                    "Mozliwosc wystawiania na bazarze 2 rzeczy"
                ]));
                break;
            case "svip":
                $player->sendMessage(MessageUtil::formatLines([
                    "§l§9KOMENDY:",
                    "/repair",
                    "/feed",
                    "/enchant",
                    "/heal",
                    "§l§9INNE:",
                    "Darmowe naprawy trzymanego przedmiotu",
                    "Mozliwosc zablokowania do 4 skrzynek",
                    "Mozliwosc wystawienia na bazarze 3 rzeczy"
                ]));
                break;
            case "sponsor":
                $player->sendMessage(MessageUtil::formatLines([
                    "§l§9KOMENDY:",
                    "/repair",
                    "/feed",
                    "/enchant",
                    "/heal",
                    "/repair all",
                    "§l§9INNE:",
                    "Darmowe naprawy przedmiotu",
                    "Darmowe naprawy wszystkich przedmiotow",
                    "Zwiekszony drop o 0.3%",
                    "Zmniejszony anty spam do 3 sekund",
                    "Mozliwosc posiadania 2 jaskin",
                    "Mozliwosc zablokowania do 6 skrzynek",
                    "Mozliwosc wystawienia na bazarze 5 rzeczy"
                ]));
                break;
            case "yt":
                $player->sendMessage(MessageUtil::formatLines([
                    "§l§9KOMENDY:",
                    "/feed",
                    "/enchant",
                    "§l§9INNE:",
                    "Mozliwosc wystawiania na bazarze 2 rzeczy",
                    "Mozliwosc wysylania linkow na czacie"
                ]));
                break;

            default:
                $player->sendMessage(MessageUtil::format("Nieznany argument!"));
                break;
        }
    }
}