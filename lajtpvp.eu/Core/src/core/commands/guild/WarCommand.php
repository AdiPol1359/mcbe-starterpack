<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\guild\war\WarInventory;
use core\guilds\GuildPlayer;
use core\Main;
use core\managers\ServerManager;
use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class WarCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("war", "", false, false, ["wojna"]);

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

        if(!Main::getInstance()->getServerManager()->isSettingEnabled(ServerManager::WARS)) {
            $sender->sendMessage(MessageUtil::format("Wojny aktualnie sa wylaczone!"));
            return;
        }

        if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName())) === null) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewz nie znajdujesz sie w zadnej gildii!"));
            return;
        }

        if(empty($args)) {
            if(($war = Main::getInstance()->getWarManager()->getWar($guild->getTag())) !== null) {
                (new WarInventory($war))->openFor([$sender]);
                return;
            }

            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["tag"]]));
            return;
        }

        $guildPlayer = $guild->getPlayer($sender->getName());

        if($guildPlayer->getRank() !== GuildPlayer::LEADER && $guildPlayer->getRank() !== GuildPlayer::OFFICER) {
            $sender->sendMessage(MessageUtil::format("Tylko lider i officer moga zarzadzac wojnami"));
            return;
        }

        if(($selectedGuild = Main::getInstance()->getGuildManager()->getGuild(implode(" ", $args))) === null) {
            $sender->sendMessage(MessageUtil::format("Gildia o podanym tagu nie istnieje!"));
            return;
        }

        if($selectedGuild->getTag() === $guild->getTag()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz wyzwac wlasna gildie na wojne!"));
            return;
        }

        if($guild->isAlliance($selectedGuild->getTag())) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz wyzwac gildii sojuszniczej na wojne!"));
            return;
        }

        if((Main::getInstance()->getWarManager()->getWar($selectedGuild->getTag())) !== null) {
            $sender->sendMessage(MessageUtil::format("Wybrana gildia jest jest w trakcie innej wojny!"));
            return;
        }

        if((Main::getInstance()->getWarManager()->getWar($guild->getTag())) !== null) {
            $sender->sendMessage(MessageUtil::format("Twoja gildia ma juz wojne z inna gildia!"));
            return;
        }

        if($selectedGuild->getConquerTime() > time()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz wywolac wojny z ta gildia poniewaz ta gildia ma jeszcze ochrone!"));
            return;
        }

        if($guild->getConquerTime() > time()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz wywolac wojny z ta gildia poniewaz twoja gildia ma jeszcze ochrone!"));
            return;
        }

        if((count($selectedGuild->getPlayers()) * 2) < count($guild->getPlayers()) || (count($guild->getPlayers()) * 2) < count($selectedGuild->getPlayers())) {
            $sender->sendMessage(MessageUtil::format("W twojej lub wybranej gildii jest za malo osob zeby moc wypowiedziec wojne!"));
            return;
        }

        Main::getInstance()->getWarManager()->createWar($guild->getTag(), $selectedGuild->getTag());

        $war = Main::getInstance()->getWarManager()->getWar($guild->getTag());

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($war, $selectedGuild, $guild) : void {
            $onlinePlayer->sendMessage(MessageUtil::formatLines(["Gildia §e".$guild->getTag()." §7wypowiedziala wojne gildii §e".$selectedGuild->getTag(), "Wojna odbedzie sie w dniu §e".(date("d.m.Y H:i:s", $war->getStartTime()))], "WOJNY"));
        });
    }
}