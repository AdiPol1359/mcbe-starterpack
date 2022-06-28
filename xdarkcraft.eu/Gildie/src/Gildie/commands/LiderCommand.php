<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\Main;
use pocketmine\Player;

class LiderCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("lider", "Komenda lider");
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

        $player = $sender->getServer()->getPlayer($args[0]);

        $pNick = $player === null ? $args[0] : $player->getName();

        $guild = $guildManager->getPlayerGuild($nick);

        $rank = $guild->getPlayerRank($nick);

        if($rank !== "Leader") {
            $sender->sendMessage(Main::format("Musisz byc liderem gildii aby to zrobic!"));
            return;
        }

        if(empty($args)) {
            $sender->sendMessage(Main::format("Poprawne uzycie: /lider (nick)"));
            return;
        }

        if(!$guild->isPlayerInGuild($pNick)) {
            $sender->sendMessage(Main::format("Tego gracza nie ma w Twojej gildii!"));
            return;
        }

        $guildManager->setDefaultPermissions($guild->getLeader());
        $guildManager->setAllPermissions($pNick);

        $guild->setPlayerRank($guild->getLeader(), "Member");
        $guild->setPlayerRank($pNick, "Leader");

        $sender->sendMessage(Main::format("Oddano lidera gildii!"));

        if($player !== null)
            $player->sendMessage(Main::format("Awansowales na lidera gidlii!"));
    }
}