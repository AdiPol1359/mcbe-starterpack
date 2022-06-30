<?php

namespace core\command\commands;

use pocketmine\command\CommandSender;

use pocketmine\{
    network\mcpe\protocol\AvailableCommandsPacket
};

use core\{
    command\BaseCommand,
    manager\managers\MuteManager,
    manager\managers\SoundManager,
    manager\managers\WebhookManager,
    util\utils\ConfigUtil,
    util\utils\MessageUtil,
    webhook\types\Embed,
    webhook\types\Message};

class MuteCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("mute", "Mute Command", true, true, "Komenda mute sluzy do wyciszania graczy", ["zmutuj"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("czas", AvailableCommandsPacket::ARG_TYPE_STRING, false),
                $this->commandParameter("powod", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args) || !isset($args[1])) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["nick"], ["czas§8(§9d§7/§9g§7/§9m§7/§9s§8)"], ["powod"]]));
            return;
        }

        is_null($this->getServer()->getPlayerExact($args[0])) ? $target = $args[0] : $target = $this->getServer()->getPlayer($args[0])->getName();

        $time = $args[1];
        $reason = "";

        for($i = 2; $i <= count($args) - 1; $i++) {
            $reason .= $args[$i];
        }

        if($reason == null)
            $reason = "BRAK";

        if(MuteManager::isMuted($target)) {
            $player->sendMessage(MessageUtil::format("Ten gracz jest juz §9Zmutowany§7!"));
            return;
        }

        switch(strtolower($time[strlen($time) - 1])) {
            case "s":
                $time = (int) str_replace('s', '', $time);
                break;
            case "m":
                $time = (int) str_replace('m', '', $time);
                $time = $time * 60;
                break;
            case "g":
                $time = (int) str_replace('g', '', $time);
                $time = $time * 3600;
                break;
            case "d":
                $time = (int) str_replace('d', '', $time);
                $time = $time * 86400;
                break;

            default:
                $player->sendMessage(MessageUtil::format("Nieznany argument"));
                return;
        }

        $player->sendMessage(MessageUtil::format("Poprawnie zmutowales gracza o nicku §9§l" . $target));
        MuteManager::setMute($target, $player, $time, $reason);
        $p = $this->getServer()->getPlayerExact($target);
        if($p != null) {
            SoundManager::addSound($p, $p->asVector3(), "block.false_permissions");
            $p->sendMessage(MessageUtil::formatLines(MuteManager::getMutedMessage($p)));
        }

        $resultTime = $args[1];

        switch(strtolower($resultTime[strlen($resultTime) - 1])) {
            case "s":
                $resultTime = (int) str_replace('s', '', $resultTime);
                if($resultTime <= 1)
                    $resultTime = $resultTime . " sekunda";
                else
                    $resultTime = $resultTime . " sekund";
                break;
            case "m":
                $resultTime = (int) str_replace('m', '', $resultTime);
                if($resultTime <= 1)
                    $resultTime = $resultTime . " minuta";
                else
                    $resultTime = $resultTime . " minut";
                break;
            case "g":
                $resultTime = (int) str_replace('g', '', $resultTime);
                if($resultTime <= 1)
                    $resultTime = $resultTime . " godzina";
                else
                    $resultTime = $resultTime . " godzin";
                break;
            case "d":
                $resultTime = (int) str_replace('d', '', $resultTime);
                if($resultTime <= 1)
                    $resultTime = $resultTime . " dzien";
                else
                    $resultTime = $resultTime . " dni";
                break;
        }

        $endMuteTime = gmdate("d.m.Y H:i", time() + ($time));

        WebhookManager::sendWebhook(new Message("", new Embed("Zmutowano gracza o nicku **__{$target}__** na **__{$resultTime}__**!", "\nPowod: **$reason**\nWygasa: **$endMuteTime**\nPrzez: **{$player->getName()}**", null, true, "https://i.ibb.co/t2nhJfH/logoDM2.png")), ConfigUtil::BLOCK_WEBHOOK);
    }
}