<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\Main;
use pocketmine\Player;

class ZastepcaCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("zastepca", "Komenda zastepca");
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

        $rank = $guild->getPlayerRank($nick);

        if($rank !== "Leader") {
            $sender->sendMessage(Main::format("Musisz byc liderem gildii, aby to zrobic!"));
            return;
        }

        if(empty($args)) {
            $sender->sendMessage(Main::format("Poprawne uzycie: /zastepca (nick)"));
            return;
        }

        $player = $sender->getServer()->getPlayer($args[0]);

        $pNick = $player === null ? $args[0] : $player->getName();

        if($guild->getPlayerRank($sender->getName()) === "Leader") {
            $sender->sendMessage(Main::format("Nie mozesz nadac sobie oficera gildii!"));
        }

        if(!$guild->isPlayerInGuild($pNick)) {
            $sender->sendMessage(Main::format("Tego gracza nie ma w Twojej gildii!"));
            return;
        }

        if($guild->getOfficer() === $pNick) {
            $guild->setPlayerRank($guild->getOfficer(), "Member");

            $sender->sendMessage(Main::format("Pomyslnie odebrano zastepce gildii graczu ".$pNick));
            if($player !== null)
                $player->sendMessage(Main::format("Zostales zwolniony z zastepcy gildii!"));
        } else {
            if($guild->getOfficer() !== null)
                $guild->setPlayerRank($guild->getOfficer(), "Member");

            $guild->setPlayerRank($pNick, "Officer");

            $sender->sendMessage(Main::format("Przydzielono zastepce gildii graczu ".$pNick));

            if($player !== null)
                $player->sendMessage(Main::format("Awansowales na zastepce gidlii!"));
        }
    }
}