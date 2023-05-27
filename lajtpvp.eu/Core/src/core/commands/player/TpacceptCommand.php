<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\guilds\GuildPlayer;
use core\Main;
use core\managers\TeleportManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class TpacceptCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("tpaccept", "", true, false);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

        if(empty($args)) {
            if(count($user->getTeleportRequests()) > 1) {
                $requests = [];

                foreach($user->getTeleportRequests() as $requestNick => $requestData)
                    $requests[] = $requestNick;

                $sender->sendMessage(MessageUtil::format("Musisz podaac nick poniewaz masz wiecej niz jedna prosba o teleportacje, twoje prosby: §e".implode("§7, §e", $requests)));
                return;
            } else {
                $request = null;

                foreach($user->getTeleportRequests() as $requestNick => $data)
                    $request = $requestNick;

                if(!$request) {
                    $sender->sendMessage(MessageUtil::format("Nie masz zadnej prosby o teleportacje!"));
                    return;
                }

                if(!($requestPlayer = $sender->getServer()->getPlayerExact($request))) {
                    $sender->sendMessage(MessageUtil::format("Osoba prosbe zaproszenie wyszla z serwera!"));
                    return;
                }

                if(TeleportManager::isTeleporting($requestPlayer->getName())) {
                    $sender->sendMessage(MessageUtil::format("Ten gracz jest juz w trakcje teleportacji!"));
                    return;
                }

                TeleportManager::teleport($requestPlayer, $sender->getPosition());
                $user->removeTeleportRequest($requestPlayer->getName());

                $sender->sendMessage(MessageUtil::format("Zaakceptowales prosbe o teleportacje od gracza §e".$requestPlayer->getName()));
                return;
            }
        }

        if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($sender->getPosition())) !== null) {
            $guildPlayer = $guild->getPlayer($sender->getName());

            if($guildPlayer) {
                if(!$guildPlayer->getSetting(GuildPlayer::TELEPORT)) {
                    $sender->sendMessage(MessageUtil::format("Nie masz uprawnien aby teleportowac na teren gildii!"));
                    return;
                }
            }
        }

        if($args[0] === "*") {
            $requests = [];

            foreach($user->getTeleportRequests() as $requestNick => $requestData) {
                if(($teleportPlayer = $sender->getServer()->getPlayerExact($requestNick)) === null)
                    continue;

                if(TeleportManager::isTeleporting($teleportPlayer->getName()))
                    continue;

                TeleportManager::teleport($teleportPlayer, $sender->getPosition());
                $requests[] = $teleportPlayer->getName();
            }

            $user->clearTeleportRequests();
            $sender->sendMessage(MessageUtil::format("Zaakceptowales prosbe o teleportacje od §8(§e".count($requests)."§8) §e".implode("§7, §e", $requests)));
            return;
        }

        if(($selectedPlayer = $sender->getServer()->getPlayerByPrefix($args[0])) === null) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest offline!"));
            return;
        }

        if($selectedPlayer->getName() === $sender->getName()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz zaakceptowac prosby o teleportacje od samego siebie!"));
            return;
        }

        if($selectedPlayer->getName() === $sender->getName()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz zaakceptowac prosby o teleportacje do samego siebie!"));
            return;
        }

        if(!$user->hasTeleportRequest($selectedPlayer->getName())) {
            $sender->sendMessage(MessageUtil::format("Ten gracz nie wyslal tobie prosby o teleportacje lub prosba wygasla!"));
            return;
        }

        TeleportManager::teleport($selectedPlayer, $sender->getPosition());
        $user->removeTeleportRequest($selectedPlayer->getName());

        $sender->sendMessage(MessageUtil::format("Zaakceptowales prosbe o teleportacje od gracza §e".$selectedPlayer->getName()));
    }
}