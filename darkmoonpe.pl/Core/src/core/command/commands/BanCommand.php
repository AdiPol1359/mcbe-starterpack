<?php

namespace core\command\commands;

use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

use core\manager\managers\BanManager;
use core\{
    command\BaseCommand,
    manager\managers\WebhookManager,
    manager\managers\SoundManager,
    util\utils\ConfigUtil,
    util\utils\MessageUtil,
    webhook\types\Embed,
    webhook\types\Message};

class BanCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("zbanuj", "Zbanuj Command", true, true, "Komenda zbanuj sluzy do banowania gracza jesli ten zlamal regulamin", ["ban"]);

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

        if(BanManager::isBanned($target)) {
            $player->sendMessage(MessageUtil::format("Ten gracz jest juz §9Zbanowany§7!"));
            return;
        }

        $time = $args[1];
        $reason = "";

        for($i = 2; $i <= count($args) - 1; $i++)
            $reason .= $args[$i];

        if($reason == null)
            $reason = "BRAK";

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
                $player->sendMessage(MessageUtil::format("Nieznany argument!"));
                return;
        }

        $player->sendMessage(MessageUtil::format("Poprawnie zbanowales gracza o nicku §9§l$target"));

        BanManager::setBan($target, $player, $time, $reason);

        $p = $this->getServer()->getPlayerExact($target);

        if($p !== null)
            $p->close("", "§l§cZOSTALES ZBANOWANY!");

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

        $endBanTime = gmdate("d.m.Y H:i:s", time() + ($time));

        foreach($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
            if($onlinePlayer->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD)
                $onlinePlayer->sendMessage(MessageUtil::formatLines(["Gracz o nicku §l§9" . $target, "Zostal zbanowany za §l§9".$reason, " Ban wygasa §l§9".$endBanTime]));
        }

        WebhookManager::sendWebhook(new Message("", new Embed("Zbanowano gracza o nicku **__{$target}__** na **__{$resultTime}__**!", "\nPowod: **$reason**\nWygasa: **$endBanTime**\nPrzez: **{$player->getName()}**", null, true, "https://i.ibb.co/t2nhJfH/logoDM2.png")), ConfigUtil::BLOCK_WEBHOOK);
    }
}