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

class BreakAllianceCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("breakalliance", "", false, false, ["zerwij"]);

        $parameters = [
            0 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["tag"]]));
            return;
        }

        if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName())) === null) {
            $sender->sendMessage(MessageUtil::format("Nie znajdujesz sie w zadnej gildii!"));
            return;
        }

        $guildPlayer = $guild->getPlayer($sender->getName());

        if(!$guildPlayer->getSetting(GuildPlayer::ALLIANCE)) {
            $sender->sendMessage(MessageUtil::format("Nie posiadasz uprawnien aby to zrobic!"));
            return;
        }

        if(($selectedGuild = Main::getInstance()->getGuildManager()->getGuild($args[0])) === null) {
            $sender->sendMessage(MessageUtil::format("Gildia o podanym tagu nie istnieje!"));
            return;
        }

        if($selectedGuild->getTag() === $guild->getTag()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz zerwac sojuszu ze swoja gildia!"));
            return;
        }

        if(!$selectedGuild->isAlliance($guild->getTag())) {
            $sender->sendMessage(MessageUtil::format("Nie masz sojuszu z ta gildia!"));
            return;
        }

        $selectedGuild->removeAlliance($guild->getTag());
        $guild->removeAlliance($selectedGuild->getTag());

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($selectedGuild, $guild) : void {
            $onlinePlayer->sendMessage(MessageUtil::format("Gildia §e" . $guild->getTag() . " §7zerwala sojusz z gildia §e" . $selectedGuild->getTag()));
        });

        NameTagPlayerManager::updatePlayersAround($sender);
    }
}