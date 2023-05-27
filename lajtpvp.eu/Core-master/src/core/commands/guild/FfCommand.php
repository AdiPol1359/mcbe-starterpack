<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\guilds\GuildPlayer;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class FfCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("ff", "", false, false, ["friendlyfire"]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        $senderGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName());

        if(!$senderGuild) {
            $sender->sendMessage(MessageUtil::format("Nie znajdujesz sie w zadnej gildii!"));
            return;
        }

        $senderGuildUser = $senderGuild->getPlayer($sender->getName());

        if(!$senderGuildUser->getSetting(GuildPlayer::FRIENDLY_FIRE)) {
            $sender->sendMessage(MessageUtil::format("Nie masz uprawnien aby zarzadzac friendly fire!"));
            return;
        }

        $senderGuild->setFriendlyFire($senderGuild->isFriendlyFireEnabled() ? false : true);

        foreach($senderGuild->getPlayers() as $guildPlayer){
            $guildMember = Server::getInstance()->getPlayerExact($guildPlayer->getName());

            if(!$guildMember)
                continue;

            $guildMember->sendTitle("§6Friendly Fire", "§r§7Zostal ".($senderGuild->isFriendlyFireEnabled() ? "§aWLACZONY" : "§cWYLACZONY")."§r§7 przez §6".$sender->getName(), 20, 20*3, 20);
        }
    }
}