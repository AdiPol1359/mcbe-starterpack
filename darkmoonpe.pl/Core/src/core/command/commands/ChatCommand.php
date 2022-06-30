<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\manager\managers\AdminManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

use core\Main;

class ChatCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("chat", "Chat Command", true, true, "Komenda chat sluzy do zarzadzania czatem gry, wlaczanie, wylaczanie, czyszczenie", ['czat']);

        $parameters = [
            0 => [
                $this->commandParameter("chatOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "chatOptions", ["off", "on", "clear"])
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args)) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["on", "off", "clear"]]));
            return;
        }

        switch($args[0]) {
            case "on":
                if(Main::$chatoff == true) {
                    Main::$chatoff = false;

                    foreach($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                        if($onlinePlayer->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD)
                            $onlinePlayer->sendMessage(MessageUtil::format("Chat zostal §9§lwlaczony"));
                    }

                    AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7wlaczyl czat"), [$player->getName()]);
                } else
                    $player->sendMessage(MessageUtil::format("Chat jest juz §9§lwlaczono"));
                break;

            case "off":
                if(Main::$chatoff == false) {
                    Main::$chatoff = true;

                    foreach($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                        if($onlinePlayer->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD)
                            $onlinePlayer->sendMessage(MessageUtil::format("Chat zostal §l§9wylaczony"));
                    }

                    AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7wylaczyl czat"), [$player->getName()]);
                } else
                    $player->sendMessage(MessageUtil::format("Chat jest juz §l§9wylaczony"));
                break;

            case "clear":

                for($i = 0; $i < 100; $i++) {
                    foreach($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                        if($onlinePlayer->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD)
                            $onlinePlayer->sendMessage(" ");
                    }
                }

                foreach($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                    if($onlinePlayer->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD)
                        $onlinePlayer->sendMessage(MessageUtil::format("Czat zostal wyczyszczony przez §l§9".$player->getName()));
                }

                break;

            default:
                $player->sendMessage(MessageUtil::format("Nieznany argument!"));
                break;

        }
    }
}