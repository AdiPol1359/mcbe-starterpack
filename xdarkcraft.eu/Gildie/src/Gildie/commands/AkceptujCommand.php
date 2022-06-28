<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\Main;
use pocketmine\Player;

class AkceptujCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("akceptuj", "Komenda akceptuj");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->canUse($sender))
            return;

        $guildManager = Main::getInstance()->getGuildManager();

        if(!$sender instanceof Player) {
            $sender->sendMessage(Main::format("Tej komendy mozesz uzyc tylko w grze!"));
            return;
        }

        if(empty($args)) {
            $sender->sendMessage(Main::format("Poprawne uzycie: /akceptuj (tag)"));
            return;
        }

        $nick = $sender->getName();

        if(!$guildManager->isInGuild($nick)) {
            $sender->sendMessage(Main::format("Musisz byc w gildii, aby uzyc tej komendy!"));
            return;
        }

        $guild = $guildManager->getPlayerGuild($nick);

        if($guild->getPlayerRank($nick) !== "Leader") {
            $sender->sendMessage(Main::format("Musisz byc liderem gildii, aby uzyc tej komendy!"));
            return;
        }

        if(!isset(Main::$alliance[$guild->getTag()]) || !in_array(strtolower($args[0]), Main::$alliance[$guild->getTag()])) {
            $sender->sendMessage(Main::format("Ta gildia nie wyslala ci prosby o sojusz!"));
            return;
        }

        $aGuild = $guildManager->getGuildByTag($args[0]);

        $guild->setAllianceWith($aGuild);
        $aGuild->setAllianceWith($guild);

        $sender->sendMessage(Main::format("Zaakceptowano sojusz z gildia ".$aGuild->getTag()));

        $aLeader = $sender->getServer()->getPlayer($aGuild->getLeader());

        if($aLeader !== null)
            $aLeader->sendMessage(Main::format("Gildia §4{$guild->getTag()} §7zaakceptowala sojusz z twoja gildia"));

        $key = array_search(strtolower($aGuild->getTag()), Main::$alliance[$guild->getTag()]);

        unset(Main::$alliance[$guild->getTag()][$key]);

        $sender->getServer()->broadcastMessage(Main::format("Gildia §8[§4{$guild->getTag()}§8] §7zawarla sojusz z gildia §8[§4{$aGuild->getTag()}§8]"));
        $guildManager->updateNameTags();
    }
}