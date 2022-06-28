<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\Main;
use pocketmine\Player;

class RozwiazCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("rozwiaz", "Komenda rozwiaz");
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
            $sender->sendMessage(Main::format("Musisz byc w gildii aby uzyc tej komendy!"));
            return;
        }

        $guild = $guildManager->getPlayerGuild($nick);

        $rank = $guild->getPlayerRank($nick);

        if($rank !== "Leader") {
            $sender->sendMessage(Main::format("Musisz byc liderem gildii aby to zrobic!"));
            return;
        }

        if(empty($args)) {
            $sender->sendMessage(Main::format("Poprawne uzycie: /rozwiaz (tag)"));
            return;
        }

        $guild = $guildManager->getPlayerGuild($sender->getName());

        if(!$guildManager->isGuildExists($args[0])) {
            $sender->sendMessage(Main::format("Ta gildia nie istnieje!"));
            return;
        }

        $aGuild = $guildManager->getGuildByTag($args[0]);

        if(!$guild->hasAllianceWith($aGuild)) {
            $sender->sendMessage(Main::format("Twoja gildia nie ma sojuszu z ta gildia!"));
            return;
        }

        $guild->removeAllianceWith($aGuild);
        $aGuild->removeAllianceWith($guild);

        $sender->getServer()->broadcastMessage(Main::format("Gildia §8[§4{$guild->getTag()}§8] §7rozwiazala sojusz z gildia §8[§4{$aGuild->getTag()}§8]"));
    }
}