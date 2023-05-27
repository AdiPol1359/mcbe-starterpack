<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\guilds\GuildPlayer;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class AfCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("af", "", false, false);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        $senderGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName());

        if(!$senderGuild) {
            $sender->sendMessage(MessageUtil::format("Nie znajdujesz sie w zadnej gildii!"));
            return;
        }

        $senderGuildUser = $senderGuild->getPlayer($sender->getName());

        if(!$senderGuildUser->getSetting(GuildPlayer::ALLIANCE_PVP)) {
            $sender->sendMessage(MessageUtil::format("Nie masz uprawnien aby zarzadzac friendly fire sojuszy!"));
            return;
        }

        $senderGuild->setAlliancePvp(!$senderGuild->isAlliancePvpEnabled());

        $senders = [];
        foreach($senderGuild->getPlayers() as $guildPlayer){
            $guildMember = Server::getInstance()->getPlayerExact($guildPlayer->getName());

            if(!$guildMember)
                continue;

            $senders[] = $guildMember->getName();
        }

        foreach($senderGuild->getAlliances() as $alliance) {
            $allianceGuild = Main::getInstance()->getGuildManager()->getGuild($alliance);

            if(!$allianceGuild)
                continue;

            foreach($allianceGuild->getOnlinePlayers() as $allianceOnlinePlayer => $allianceRank) {

                $guildAllianceMember = Server::getInstance()->getPlayerExact($allianceOnlinePlayer);

                if(!$guildAllianceMember)
                    continue;

                $senders[] = $allianceOnlinePlayer;
            }
        }

        foreach($senders as $dataPlayer) {
            if(($p = Server::getInstance()->getPlayerExact($dataPlayer)))
                $p->sendTitle("§6Sojuszniczy ogien", "§r§7Zostal ".($senderGuild->isAlliancePvpEnabled() ? "§aWLACZONY" : "§cWYLACZONY")."§r§7 przez §6".$sender->getName(), 20, 20*3, 20);
        }
    }
}