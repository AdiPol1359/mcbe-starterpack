<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\Main;
use pocketmine\Player;
use pocketmine\item\Item;

class SojuszCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("sojusz", "Komenda sojusz");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->canUse($sender))
            return;

        $guildManager = Main::getInstance()->getGuildManager();

        $nick = $sender->getName();

        if(!$sender instanceof Player) {
            $sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
            return;
        }


        if(!$guildManager->isInGuild($nick)) {
            $sender->sendMessage(Main::format("Musisz byc w gildii, aby uzyc tej komendy!"));
            return;
        }

        $guild = $guildManager->getPlayerGuild($nick);

        if($guild->getPlayerRank($nick) !== "Leader") {
            $sender->sendMessage(Main::format("Musisz byc liderem gildii, aby uzyc tej komendy!"));
            return;
        }

        if(empty($args)) {
            $sender->sendMessage(Main::format("Poprawne uzycie: /sojusz (gildia)"));
            return;
        }

        if(!$guildManager->isGuildExists($args[0])) {
            $sender->sendMessage(Main::format("Ta gildia nie istnieje!"));
            return;
        }

        $aGuild = $guildManager->getGuildByTag($args[0]);

        if($guild->getTag() === $aGuild->getTag()) {
            $sender->sendMessage(Main::format("Nie mozesz zawrzec sojuszu ze swoja gildia!"));
            return;
        }

        if($guild->hasAllianceWith($aGuild)) {
            $sender->sendMessage(Main::format("Twoja gildia ma juz sojusz z ta gildia!"));
            return;
        }

        $aLeader = $sender->getServer()->getPlayer($aGuild->getLeader());

        if($aLeader === null) {
            $sender->sendMessage(Main::format("Lider tej gildii jest offline!"));
            return;
        }

        Main::$alliance[$aGuild->getTag()][] = strtolower($guild->getTag());

        $sender->sendMessage(Main::format("Zaproszono do sojuszu gildie §8[§4{$aGuild->getTag()}§8]"));

        $aLeader->sendMessage(Main::formatLines(["Gildia §4{$guild->getTag()} §7wyslala prosbe o sojusz", "Uzyj §4/akceptuj §4{$guild->getTag()}§7, aby ja zaakceptowac"]));
    }
}