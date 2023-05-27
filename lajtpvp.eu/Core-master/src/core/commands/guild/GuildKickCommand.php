<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\guilds\GuildPlayer;
use core\Main;
use core\managers\nameTag\NameTagPlayerManager;
use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class GuildKickCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("guildkick", "", false, false, ["wyrzuc"]);

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

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nick"]]));
            return;
        }

        $senderGuild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName());

        if(!$senderGuild) {
            $sender->sendMessage(MessageUtil::format("Nie znajdujesz sie w zadnej gildii!"));
            return;
        }

        $nick = implode(" ", $args);

        $senderGuildUser = $senderGuild->getPlayer($sender->getName());

        if(!$senderGuildUser->getSetting(GuildPlayer::KICK_PLAYER)) {
            $sender->sendMessage(MessageUtil::format("Nie masz uprawnien aby to zrobic!"));
            return;
        }

        $selectedPlayerGuild = $senderGuild->getPlayer($nick);

        if(!$selectedPlayerGuild) {
            $sender->sendMessage(MessageUtil::format("Ten gracz nie znajduje sie w twojej gildii!"));
            return;
        }

        if($selectedPlayerGuild->getRank() === GuildPlayer::LEADER) {
            $sender->sendMessage(MessageUtil::format("Nie mozna wyrzucic lidera z gildii!"));
            return;
        }

        if($selectedPlayerGuild->getRank() === GuildPlayer::OFFICER && $senderGuildUser->getRank() !== GuildPlayer::LEADER) {
            $sender->sendMessage(MessageUtil::format("Tylko lider moze wyrzucic oficerow z gildii!"));
            return;
        }

        if($nick === $sender->getName()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz wyrzucic samego siebie z gildii!"));
            return;
        }

        $senderGuild->kickPlayer($nick);

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($nick, $senderGuild) : void {
            $onlinePlayer->sendMessage(MessageUtil::format("Gracz §e" . $nick . "§7 zostal wyrzucony z gildii §e" . $senderGuild->getTag() . "§7!"));
        });

        NameTagPlayerManager::updatePlayersAround($sender);
    }
}