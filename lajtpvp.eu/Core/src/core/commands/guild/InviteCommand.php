<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\guilds\GuildPlayer;
use core\Main;
use core\utils\BroadcastUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class InviteCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("invite", "", false, false, ["zapros", "dodaj"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ],

            1 => [
                $this->commandParameter("inviteOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "inviteOptions", ["all"])
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nick", "all"]]));
            return;
        }

        $senderGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName());

        if(!$senderGuild) {
            $sender->sendMessage(MessageUtil::format("Nie znajdujesz sie w zadnej gildii!"));
            return;
        }

        if($args[0] === "all") {

            $senders = [];

            BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($senderGuild, $sender, &$senders) : void {
                if($onlinePlayer->getName() === $sender->getName())
                    return;

                if($onlinePlayer->distance($sender) <= 5) {
                    if(($onlinePlayerGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($onlinePlayer->getName())) !== null)
                        return;

                    if(($closeUser = Main::getInstance()->getUserManager()->getUser($onlinePlayer->getName())) === null)
                        return;

                    if($closeUser->hasInvite($senderGuild->getTag()))
                        return;

                    $senders[] = $onlinePlayer;
                }
            });

            $count = 0;
            foreach($senders as $closePlayer) {

                $senderGuildUser = $senderGuild->getPlayer($sender->getName());

                if(!$senderGuildUser->getSetting(GuildPlayer::ADD_PLAYER)) {
                    $sender->sendMessage(MessageUtil::format("Nie masz uprawnien aby to zrobic!"));
                    return;
                }

                Main::getInstance()->getUserManager()->getUser($closePlayer->getName())->addInvite($senderGuild->getTag());
                $closePlayer->sendMessage(MessageUtil::formatLines(["Otrzymales zaproszenie do gildii §e" . $senderGuild->getTag() . "§r§7!", "§7Masz §e60 §7sekund na akceptacje!", "Aby zaakceptowac wpisz §8/§edolacz " . $senderGuild->getTag()]));
                $count++;
            }

            $sender->sendMessage(MessageUtil::format("Wyslales zaproszenie gildyjne do §e".$count." §7osob!"));
            return;
        }

        $nick = implode(" ", $args);

        if($nick === $sender->getName()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz zaprosic samego siebie do gildii!"));
            return;
        }

        if($senderGuild->existsPlayer($nick)) {
            $sender->sendMessage(MessageUtil::format("Ten gracz znajduje juz sie w tej gildii!"));
            return;
        }

        $selectedPlayer = $sender->getServer()->getPlayerExact($nick);

        if(!$selectedPlayer) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest offline!"));
            return;
        }

        $senderGuildUser = $senderGuild->getPlayer($sender->getName());

        if(!$senderGuildUser->getSetting(GuildPlayer::ADD_PLAYER)) {
            $sender->sendMessage(MessageUtil::format("Nie masz uprawnien aby to zrobic!"));
            return;
        }

        if(count($senderGuild->getPlayers()) >= Settings::$GUILD_MEMBERS_LIMIT || count($senderGuild->getPlayers()) >= $senderGuild->getSlots()) {
            $sender->sendMessage(MessageUtil::format("Osiagnales limit czlonkow w gildii!"));
            return;
        }

        if(($selectedUser = Main::getInstance()->getUserManager()->getUser($nick)) === null) {
            $sender->sendMessage(MessageUtil::format("Ten gracz nigdy nie gral na tym serwerze!"));
            return;
        }

        if($selectedUser->hasInvite($senderGuild->getTag())) {
            $sender->sendMessage(MessageUtil::format("Ten gracz otrzymal juz od ciebie jedno zaproszenie do gildii!"));
            return;
        }

        $selectedUser->addInvite($senderGuild->getTag());

        $sender->sendMessage(MessageUtil::formatLines(["Wyslales zaproszenie do §e" . $nick, "Ma on §e".Settings::$INVITE_EXPIRE_TIME."s§7 na zaakceptowanie!"]));
        $selectedPlayer->sendMessage(MessageUtil::formatLines(["Otrzymales zaproszenie do gildii §e".$senderGuild->getTag()."§r§7!", "§7Masz §e60 §7sekund na akceptacje!", "Aby zaakceptowac wpisz §8/§edolacz ".$senderGuild->getTag()]));
    }
}