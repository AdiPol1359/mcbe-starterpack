<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class WhitelistCommand extends BaseCommand{

    public function __construct(){
        parent::__construct("whitelist", "", true, true, ["wl"]);

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

    public function onCommand(CommandSender $sender, array $args) : void{
        if(empty($args)){
            $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Dodaje gracza do whitelist'y" => ["add", "§8(§enick§8)"], "Usuwa gracza do whitelist'y" => ["remove", "§8(§enick§8)"], "Pokazuje liste osob na whitelistcie" => ["list"], "Wylacza whiteliste" => ["off"], "Wlacza whiteliste" => ["on"], "Ustawia czas wylaczenia whitelisty" => ["settime", "§eD§7.§eM§7.§eY", "§eH§7:§eM"]]));
            return;
        }
        
        $whitelistManager = Main::getInstance()->getWhitelistManager();
        
        switch($args[0]){
            case "add":
                if(!isset($args[1])){
                    $sender->sendMessage(MessageUtil::format("Nie podano nicku!"));
                    return;
                }

                $whitelistManager->addPlayer($args[1]);
                $sender->sendMessage(MessageUtil::format("Poprawnie dodano §e".$args[1]."§r§7 do whitelisty!"));

                AdminManager::sendMessage($sender, $sender->getName() . " dodal do whitelisty ".$args[1]);
                break;

            case "remove":
                if(!isset($args[1])){
                    $sender->sendMessage(MessageUtil::format("Nie podano nicku!"));
                    return;
                }

                $whitelistManager->removePlayer($args[1]);
                $sender->sendMessage(MessageUtil::format("Poprawnie usunieto §e".$args[1]."§r§7 z whitelisty!"));

                AdminManager::sendMessage($sender, $sender->getName() . " usunal z whitelisty ".$args[1]);
                break;

            case "list":
                $list = implode("§r§7, §e", $whitelistManager->getWhitelistPlayers());
                $sender->sendMessage(MessageUtil::format("Osoby na whiteliscie: §e".$list));
                break;

            case "off":
                $whitelistManager->setWhitelist(false);
                $sender->sendMessage(MessageUtil::format("Wylaczono whiteliste!"));

                AdminManager::sendMessage($sender, $sender->getName() . " wylaczyl whiteliste");
                break;

            case "on":
                $whitelistManager->setWhitelist(true);
                $sender->sendMessage(MessageUtil::format("Wlaczono whiteliste!"));

                AdminManager::sendMessage($sender, $sender->getName() . " wlaczyl whiteliste");
                break;

            case "settime":
                if(!isset($args[2])) {
                    $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Dodaje gracza do whitelist'y" => ["add", "§8(§enick§8)"], "Usuwa gracza do whitelist'y" => ["remove", "§8(§enick§8)"], "Pokazuje liste osob na whitelistcie" => ["list"], "Wylacza whiteliste" => ["off"], "Wlacza whiteliste" => ["on"], "Ustawia czas wylaczenia whitelisty" => ["settime", "§eD§7.§eM§7.§eY", "§eH§7:§eM"]]));
                    return;
                }

                $hm = explode(':', $args[2]);

                if(!is_numeric($hm[0]) || !is_numeric($hm[1]) || $hm[0] > 24 || $hm[1] > 59) {
                    $sender->sendMessage(MessageUtil::format("Nieprawidlowy format godziny!"));
                    return;
                }

                $date = $args[1] . " " . $args[2];

                if(time() > strtotime($date)) {
                    $sender->sendMessage(MessageUtil::format("Nieprawidlowa data!"));
                    return;
                }

                $whitelistManager->setWhitelistDate($date);
                $sender->sendMessage(MessageUtil::format("Pomyslnie ustawiono date wylaczenia lobby na §e$date"));

                AdminManager::sendMessage($sender, $sender->getName() . " ustawil date wylaczenia whiteslisty na ".$date);
                break;
            default:
                $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                break;
        }
    }
}