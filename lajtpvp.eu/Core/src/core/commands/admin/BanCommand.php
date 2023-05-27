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

class BanCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("ban", "", true, true, ["zbanuj"]);

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

        $banPlayer = $args[0];
        $time = $args[1];

        if(Main::getInstance()->getBanManager()->isBanned($banPlayer)) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest juz zbanowany!"));
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

        $banTime = $time + time();

        $banUser = Main::getInstance()->getUserManager()->getUser($banPlayer);
        $targetPlayer = $sender->getServer()->getPlayerByPrefix($banPlayer);

        Main::getInstance()->getBanManager()->setBan(($targetPlayer ? $targetPlayer->getName() : $banPlayer), ($targetPlayer?->getNetworkSession()->getIp()), ($banUser?->getDeviceId()), $sender->getName(), $reason, $banTime);

        $targetPlayer?->kick("", Main::getInstance()->getBanManager()->getBanFormat(Main::getInstance()->getBanManager()->getBanNickInfo($targetPlayer->getname())));

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($banTime, $reason, $banPlayer, $targetPlayer) : void {
            $onlinePlayer->sendMessage(MessageUtil::formatLines(["Zbanowano gracza §e" . ($targetPlayer ? $targetPlayer->getName() : $banPlayer), "Powod bana §e" . $reason, "Ban wygasa §e" . date("d.m.Y H:i:s", $banTime)], "ZBANOWANO GRACZA"));
        });

        WebhookUtil::sendWebhook(new Message("", new Embed("Zbanowano gracza o nicku **__".($targetPlayer ? $targetPlayer->getName() : $banPlayer)."__**!", "\nPowod: **$reason**\nWygasa: **".date("d.m.Y H:i:s", $banTime)."**\nPrzez: **{$sender->getName()}**", null, true)), Settings::$BAN_WEBHOOK);
        $sender->sendMessage(MessageUtil::format("Poprawnie zbanowano gracza!"));
    }
}