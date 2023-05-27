<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\guilds\GuildPlayer;
use core\Main;
use core\managers\nameTag\NameTagPlayerManager;
use core\utils\BroadcastUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class AllianceCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("alliance", "", false, false, ["sojusz"]);

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
            $sender->sendMessage(MessageUtil::format("Nie masz uprawnien aby zapraszac do sojuszu!"));
            return;
        }

        if(($selectedGuild = Main::getInstance()->getGuildManager()->getGuild($args[0])) === null) {
            $sender->sendMessage(MessageUtil::format("Gildia o takim tagu nie istnieje!"));
            return;
        }

        if($selectedGuild->getTag() === $guild->getTag()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz zaprosic do sojuszu wlasnej gildii!"));
            return;
        }

        if($selectedGuild->isAlliance($guild->getTag())) {
            $sender->sendMessage(MessageUtil::format("Twoja gildia juz posiada sojusz z ta gildia!"));
            return;
        }

        if(count($guild->getAlliances()) >= Settings::$GUILD_ALLIANCES_LIMIT || count($selectedGuild->getAlliances()) >= Settings::$GUILD_ALLIANCES_LIMIT) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz wyslac prosbe o sojusz poniewaz twoja lub gildie ktorej wysylasz zaproszenie osiagnela limit sojuszy! §8(§e".Settings::$GUILD_ALLIANCES_LIMIT."§8)"));
            return;
        }

        if($guild->hasAllianceRequest($selectedGuild->getTag())) {
            $selectedGuild->addAlliance($guild->getTag());
            $guild->addAlliance($selectedGuild->getTag());

            BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($selectedGuild, $guild) : void {
                $onlinePlayer->sendMessage(MessageUtil::format("Gildia §e".$guild->getTag()." §7nawiazala sojusz z gildia §e".$selectedGuild->getTag()));
            });

            NameTagPlayerManager::updatePlayersAround($sender);
            return;
        }

        if($selectedGuild->hasAllianceRequest($guild->getTag())) {
            $sender->sendMessage(MessageUtil::format("Juz wyslales prosbe o sojusz!"));
            return;
        }

        $selectedGuild->addAllianceRequest($guild->getTag());

        foreach($selectedGuild->getPlayers() as $guildPlayer) {
            if(($onlineGuildPlayer = Server::getInstance()->getPlayerExact($guildPlayer->getName())))
                $onlineGuildPlayer->sendMessage(MessageUtil::formatLines(["Gildia §e".$guild->getTag()." §7wysylala prosbe o sojusz", "Aby zaakaceptowac wpisz §8/§esojusz ".$guild->getTag()], "SOJUSZ GILDII"));
        }

        foreach($guild->getPlayers() as $guildPlayer) {
            if(($onlineGuildPlayer = Server::getInstance()->getPlayerExact($guildPlayer->getName())))
                $onlineGuildPlayer->sendMessage(MessageUtil::format("Twoja gildia wysylala prosbe o sojusz do gildii §e".$selectedGuild->getTag()));
        }
    }
}