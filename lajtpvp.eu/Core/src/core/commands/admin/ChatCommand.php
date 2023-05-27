<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use core\utils\Settings;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class ChatCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("chat", "", true, true, ["czat"]);

        $parameters = [
            0 => [
                $this->commandParameter("chatOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "chatOptions", ["off", "on", "clear"])
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(empty($args)){
            $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Czysci czat" => ["clear"], "Wlacza czat" => ["on"], "Wylacza czat" => ["off"]]));
            return;
        }

        switch($args[0]){
            case "clear":

                BroadcastUtil::broadcastCallback(function($onlinePlayer) : void {
                    for($i = 0; $i < 100; $i++) {
                        $onlinePlayer->sendMessage(" ");
                    }
                });

                BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($sender) : void {
                    $onlinePlayer->sendMessage(MessageUtil::format("Chat zostal wyczyszczony przez §e" . $sender->getName() . "§r§7!"));
                });

                break;

            case "on":

                if(Settings::$CHAT){
                    $sender->sendMessage(MessageUtil::format("Chat jest juz §eWLACZONY§r§7!"));
                    return;
                }

                Settings::$CHAT = true;

                BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($sender) : void {
                    $onlinePlayer->sendMessage(MessageUtil::format("Chat zostal wlaczony przez administratora §e" . $sender->getName() . "§r§7!"));
                });

                break;

            case "off":

                if(!Settings::$CHAT){
                    $sender->sendMessage(MessageUtil::format("Chat jest juz §eWYLACZONY§r§7!"));
                    return;
                }

                Settings::$CHAT = false;

                BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($sender) : void {
                    $onlinePlayer->sendMessage(MessageUtil::format("Chat zostal wylaczony przez administratora §e" . $sender->getName() . "§r§7!"));
                });

                break;

            default:
                $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Czysci czat" => ["clear"], "Wlacza czat" => ["on"], "Wylacza czat" => ["off"]]));
                break;
        }
    }
}