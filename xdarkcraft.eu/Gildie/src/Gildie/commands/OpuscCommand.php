<?php

namespace Gildie\commands;

use pocketmine\command\{Command, CommandSender, utils\CommandException};
use Gildie\Main;
use pocketmine\Player;

class OpuscCommand extends GuildCommand {

    public function __construct() {
        parent::__construct("opusc", "Komenda opusc");
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

        if($guild->getPlayerRank($nick) == "Leader") {
            $sender->sendMessage(Main::format("Musisz oddac lidera, aby uzyc tej opcji!"));
            return;
        }

        $guild->removePlayer($nick);

        $sender->getServer()->broadcastMessage(Main::format("Gracz §4{$nick} §7opuscil gildie §8[§4{$guild->getTag()}§8]"));
    }
}