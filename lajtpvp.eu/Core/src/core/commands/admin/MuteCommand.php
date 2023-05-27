<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\utils\BroadcastUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use core\utils\WebhookUtil;
use core\webhooks\types\Embed;
use core\webhooks\types\Message;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class MuteCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("mute", "", true, true, ["zmutuj"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("czas", AvailableCommandsPacket::ARG_TYPE_STRING, false),
                $this->commandParameter("powod", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args) || !isset($args[1])) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nick"], ["czas"], ["powod"]]));
            return;
        }

        $mutePlayer = $args[0];
        $time = $args[1];

        if(Main::getInstance()->getMuteManager()->isMuted($mutePlayer)) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest juz zmutowany!"));
            return;
        }

        $reason = "";

        if(isset($args[2])) {
            for($i = 2; $i <= (count($args) - 1); $i++)
                $reason .= $args[$i] . " ";
        } else
            $reason = "BRAK";

        switch(strtolower($time[strlen($time) - 1])) {
            case "s":
                $time = (int) str_replace('s', '', $time);
                break;
            case "m":
                $time = (int) str_replace('m', '', $time);
                $time = $time * 60;
                break;
            case "h":
                $time = (int) str_replace('g', '', $time);
                $time = $time * 3600;
                break;
            case "d":
                $time = (int) str_replace('d', '', $time);
                $time = $time * 86400;
                break;

            default:
                $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                return;
        }

        $targetPlayer = $sender->getServer()->getPlayerByPrefix($mutePlayer);

        Main::getInstance()->getMuteManager()->setMute(($targetPlayer ? $targetPlayer->getName() : $mutePlayer), $sender->getName(), $reason, (time() + $time));

        $targetPlayer?->sendMessage(MessageUtil::formatLines(Main::getInstance()->getMuteManager()->getMuteFormat(Main::getInstance()->getMuteManager()->getMuteNickInfo($targetPlayer->getName())), "MUTE"));

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($time, $reason, $mutePlayer, $targetPlayer) : void {
            $onlinePlayer->sendMessage(MessageUtil::formatLines(["Zmutowano gracza §e" . ($targetPlayer ? $targetPlayer->getName() : $mutePlayer), "Powod mutea §e" . $reason, "Mute wygasa §e" . date("d.m.Y H:i:s", (time() + $time))], "ZMUTOWANO GRACZA"));
        });

        WebhookUtil::sendWebhook(new Message("", new Embed("Zmutowano gracza o nicku **__".($targetPlayer ? $targetPlayer->getName() : $mutePlayer)."__**!", "\nPowod: **$reason**\nWygasa: **".date("d.m.Y H:i:s", (time() + $time))."**\nPrzez: **{$sender->getName()}**", null, true)), Settings::$MUTE_WEBHOOK);
        $sender->sendMessage(MessageUtil::format("Poprawnie zmutowano gracza!"));
    }
}