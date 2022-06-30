<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\Main;
use core\manager\managers\WhitelistManager;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class WhitelistCommand extends BaseCommand{
    public function __construct(){
        parent::__construct("whitelist", "Whitelist Command", true, true, "Komenda whitelist sluzy do zarzadzania lista graczy ktorzy sa upowaznieni do wejscia podczas prac nad serwerem", ["wl"]);

        $parameters = [
            0 => [
                $this->commandParameter("whitelistPlayer", AvailableCommandsPacket::ARG_TYPE_STRING, false, "whitelistPlayer", ["add", "remove"]),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ],

            1 => [
                $this->commandParameter("whitelistOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "whitelistOptions", ["off", "on", "list"]),
            ],

            2 => [
                $this->commandParameter("whitelistTime", AvailableCommandsPacket::ARG_TYPE_STRING, false, "whitelistTime", ["settime"]),
                $this->commandParameter("data", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void{
        if(empty($args)){
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["add", "remove", "list", "off", "on", "settime"], ["nick", "czas"]]));
            return;
        }

        $mainCfg = Main::getCfg();

        switch($args[0]){
            case "add":
                if(!isset($args[1])){
                    $player->sendMessage(MessageUtil::format("Nie podano nicku!"));
                    return;
                }

                WhitelistManager::addPlayer($args[1]);
                $player->sendMessage(MessageUtil::format("Poprawnie dodano §l§9".$args[1]."§r§7 do whitelisty!"));
                break;

            case "remove":
                if(!isset($args[1])){
                    $player->sendMessage(MessageUtil::format("Nie podano nicku!"));
                    return;
                }

                WhitelistManager::removePlayer($args[1]);
                $player->sendMessage(MessageUtil::format("Poprawnie usunieto §l§9".$args[1]."§r§7 z whitelisty!"));
                break;

            case "list":
                $list = implode("§r§7, §l§9", WhitelistManager::getWhitelistPlayers());
                $player->sendMessage(MessageUtil::format("Osoby na whiteliscie: §l§9".$list));
                break;

            case "off":
                WhitelistManager::setWhitelist(false);
                $player->sendMessage(MessageUtil::format("Wylaczono whiteliste!"));
                break;

            case "on":
                WhitelistManager::setWhitelist(true);
                $player->sendMessage(MessageUtil::format("Wlaczono whiteliste!"));
                break;

            case "settime":
                if(!isset($args[2])) {
                    $player->sendMessage($this->correctUse($this->getCommandLabel(), [["settime"], ["D§7.§9M§7.§9Y"], ["H§7:§9M"]]));
                    return;
                }

                $hm = explode(':', $args[2]);

                if(!is_numeric($hm[0]) || !is_numeric($hm[1]) || $hm[0] > 24 || $hm[1] > 59) {
                    $player->sendMessage(MessageUtil::format("Nieprawidlowy format godziny!"));
                    return;
                }

                $date = $args[1] . " " . $args[2];

                if(time() > strtotime($date)) {
                    $player->sendMessage(MessageUtil::format("Nieprawidlowa data!"));
                    return;
                }

                WhitelistManager::setWhitelistDate($date);
                $player->sendMessage(MessageUtil::format("Pomyslnie ustawiono date wylaczenia lobby na §9§l$date"));
                break;
            default:
                $player->sendMessage(MessageUtil::format("Nieznany argument!"));
                break;
        }
        $mainCfg->save();
    }
}