<?php

declare(strict_types=1);

namespace core\commands\guild;

use core\commands\BaseCommand;
use core\guilds\GuildPlayer;
use core\Main;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Server;

class InfoCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("information", "", false, true, ["info"]);

        $parameters = [
            0 => [
                $this->commandParameter("tag", AvailableCommandsPacket::ARG_TYPE_STRING, false, "guildTag", Main::getInstance()->getGuildManager()->getGuildsTags(true)),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        $selectedGuild = null;

        if(empty($args)) {
            if(($guild = Main::getInstance()->getGuildManager()->getPlayerGuild($sender->getName())) === null) {
                $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["tag"]]));
                return;
            }

            $selectedGuild = $guild;
        } else {
            if(($guild = Main::getInstance()->getGuildManager()->getGuild($args[0])) === null) {
                $sender->sendMessage(MessageUtil::format("Gildia o takim tagu nie istnieje!"));
                return;
            }

            $selectedGuild = $guild;
        }

        $senders = "";
        $sendersCount = 0;

        foreach($selectedGuild->getPlayers() as $guildPlayer) {

            $format = "";
            $sendersCount++;

            if($guildPlayer->getRank() === GuildPlayer::LEADER || $guildPlayer->getRank() === GuildPlayer::OFFICER)
                $format .= "§l";

            $senders .= Server::getInstance()->getPlayerExact($guildPlayer->getName()) ? $format."§a" . $guildPlayer->getName() . ($sendersCount < count($selectedGuild->getPlayers()) ? "§r§7, " : "") : $format."§c" . $guildPlayer->getName() . ($sendersCount < count($selectedGuild->getPlayers()) ? "§r§7, " : "");
        }

        $alliances = [];

        foreach($selectedGuild->getAlliances() as $key => $alliance)
            $alliances[] = $alliance;

        $sender->sendMessage(MessageUtil::formatLines(
            [
                "§7Tag§8: §e".$selectedGuild->getTag(),
                "§7Nazwa§8: §e".$selectedGuild->getName(),
                "§7Punkty§8: §e".$selectedGuild->getPoints(),
                "§7Lider§8: §e".$selectedGuild->getLeader()->getName(),
                "§7Teren§8: §8(§e".$selectedGuild->getSize()."§8x§e".$selectedGuild->getSize()."§8)",
                "§7Wygasa§8: §e".(date("d.m.Y H:i", $selectedGuild->getExpireTime())),
                "§7Ochrona§8: §e".(date("d.m.Y H:i", $selectedGuild->getConquerTime())),
                "§7Serca§8: §8(§e".$selectedGuild->getHearts()."§8/§e5§8)",
                "§7Zdrowie§8: §8(§e".$selectedGuild->getHealth()."§8/§e".Settings::$MAX_GUILD_HEALTH."§8)",
                "§7Sojusze§8: §e".(!empty($alliances) ? implode("§7, §e", $alliances) : "BRAK"),
                "§7Kara TNT§8: ".($guild->isTntEnabled() ? "§aWlaczona" : "§cWylaczona"),
                "§7Koordynaty§8: §eX§7/§eZ §8(§e".$guild->getHeartSpawn()->x."§7/§e".$guild->getHeartSpawn()->z."§8)",
                "§7Czlonkowie§8: §8(§a".count($selectedGuild->getOnlinePlayers())."§7/§c".(count($selectedGuild->getPlayers()) - count($selectedGuild->getOnlinePlayers()))."§7/§e".count($selectedGuild->getPlayers())."§8) ".$senders
            ]
            , "GILDIA"));
    }
}